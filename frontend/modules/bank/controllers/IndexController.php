<?php

namespace frontend\modules\bank\controllers;

use common\models\bank\Order;
use common\models\bank\Product;
use common\models\bank\Profit;
use common\models\bank\Log;
use common\models\Wallet;
use common\models\CurrencyPrice;
use common\models\SupernodeProfit;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;

/**
 * 令牌理财
 */
class IndexController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [''],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'info', 'add-order'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $get = Yii::$app->request->queryParams;
        if (isset($get['userid'])) {
            unset($get['userid']);
            return $this->redirect('/' . Yii::$app->request->pathInfo . '?' . http_build_query($get));
        }

        //  累积收益
        $profit['total'] = Profit::find()->where(['uid' => Yii::$app->user->identity->id])->sum('amount');

        $symbolPrice = CurrencyPrice::find()->where(['symbol' => 'ANT'])->one();

        $total['amount'] = Order::find()->where(['AND', ['=', 'uid', Yii::$app->user->identity->id], ['>=', 'status', Order::STATUS_LOCK]])->sum('amount');
        $total['amount'] = $total['amount'] + $profit['total'];
        $total['rmb'] = $profit['total'] * $symbolPrice->price;
        //  昨日收益
        $statime = strtotime(date('Y-m-d')) - 86400;
        $endtime = $statime + 86399;
        $profit['lastday'] = Profit::find()->where([
            'AND',
            ['between', 'created_at', $statime, $endtime],
            ['=', 'uid', Yii::$app->user->identity->id],
        ])->sum('amount');

        //  理财列表
        $list = Product::find()->where(['status' => Product::STATUS_ACTIVE])->orderBy('id desc')->all();

        return $this->renderPartial('index', [
            'total' => $total,
            'profit' => $profit,
            'list' => $list,
        ]);
    }

    /**
     * 产品详情
     *
     * @param [type] $id
     * @return void
     */
    public function actionInfo($id)
    {
        $product = Product::find()->where(['id' => $id])->one();

        $wallet = Wallet::find()
            ->with(['currencyPrice'])
            ->where(['user_id' => Yii::$app->user->identity->id, 'symbol' => $product->symbol])
            ->one();

        //  投注额度
        $amount = Order::find()->where(['product_id' => $product->id])->sum('amount');

        //  计算已占额比例
        $amount = $amount ?: 0;
        $rate = $amount / $product->max_amount * 100;

        //  默认收益
        $userAmount = $wallet['amount'] - $wallet['amount_lock'];
        $push['amount'] = $userAmount * 0.25;
        $push['profit'] = (($push['amount'] * $product->rate * $product->currency_price / 100) / $product->earn_currency_price);

        return $this->renderPartial('info', [
            'product' => $product,
            'wallet' => $wallet,
            'amount' => $amount,
            'rate' => $rate,
            'push' => $push,
        ]);
    }

    /**
     * 提交订单
     *
     * @return void
     */
    public function actionAddOrder()
    {
        if (Yii::$app->request->isPost) {
            $tr = Yii::$app->db->beginTransaction();
            try {
                $model = new Order();
                $model->scenario = 'insert';
                $model->status = 0;

                if ($model->load(Yii::$app->request->post()) && false !== $model->save()) {
                    //  消费金额
                    $model->spendAmount();
                    //  锁仓银行日志
                    Log::bankLog(Log::TYPE_LOCK_PRODUCT, $model);
                    //  更新产品数量
                    $product = Product::updateAmount($model->product_id);

                    //收益币种
                    $earnSymbol = Product::getEarnSymbol($model->product_id);

                    //  写入预期收益
                    /*$amount = $model->amount * $product->super_rate / 100;
                    SupernodeProfit::addProfit($amount , $earnSymbol, SupernodeProfit::TYPE_BANK_EXPECT, $model);*/

                    //  发送通知
                    $user = \api\models\User::findOne($model->uid);
                    \common\models\Message::addMessage(\common\models\Message::TYPE_FINANCIAL_BUY, $user, $model->symbol, $model->amount, $model);

                    $tr->commit();
                    return Json::encode([
                        'status' => 1,
                    ]);
                } else {
                    return Json::encode([
                        'status' => 0,
                        'msg' => $model->getErrorSummary(false)[0],
                    ]);
                }
            } catch (\ErrorException $e) {
                $msg = $e->getMessage();
                $tr->rollBack();
            }
            return Json::encode([
                'status' => 0,
                'msg' => $msg,
            ]);
        }
    }
}
