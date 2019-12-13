<?php

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\modules\v1\controllers\UserController;
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
class LiCaiController extends BaseController
{

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $lang = Yii::$app->request->headers['Lang'];
        //  累积收益
        $profit['total'] = Profit::find()->where(['uid' => Yii::$app->user->identity->id,'type' => Profit::TYPE_PROFIT,'symbol'=>'MFCC'])->sum('amount');
        //获取币种价值
        $symbolPrice = CurrencyPrice::find()->all();

        $total['amount'] = Order::find()->where(['AND', ['=', 'uid', Yii::$app->user->identity->id], ['>=', 'status', Order::STATUS_LOCK]])->sum('amount');
        $total['amount'] = $total['amount'] + $profit['total'];
        $profit['total'] = round($profit['total'],4);//币种数量保留4位
        $total['amount'] = round($total['amount'],4);//币种数量保留4位
        foreach ($symbolPrice as $k=>$v){
            if($v->symbol == 'MFCC'){
                $total['rmb'] = $profit['total'] * $v->price;
            }
        }
        $total['rmb'] = round($total['rmb'],2);//rmb保留2位
        //  昨日收益
        $statime = strtotime(date('Y-m-d')) - 86400;
        $endtime = $statime + 86399;
        $profit['lastday'] = Profit::find()->where([
            'AND',
            ['between', 'created_at', $statime, $endtime],
            ['=', 'uid', Yii::$app->user->identity->id],
            ['=', 'symbol', 'MFCC'],
        ])->sum('amount');
        $profit['lastday'] = round($profit['lastday'],4);//rmb保留4位
        //  理财列表
        $list = Product::find()
            ->where(['status' => Product::STATUS_ACTIVE])
            ->asArray()
            ->orderBy('id desc')
            ->all();
        foreach ($list as $k =>$v){
            $product_list[$k]['id'] = sprintf("%.2f", $v['id']);
            $product_list[$k]['rate'] = sprintf("%.2f", $v['rate']);//每日收益率
            $product_list[$k]['earn_symbol'] =  $v['earn_symbol'];//收益币种
            $product_list[$k]['expected_earnings'] =  round($v['rate'] * $v['currency_price'] / $v['earn_currency_price'],2);//预计收益
            switch ($lang){
                case 'zh':
                    $product_list[$k]['name'] = $v['name_zh'];
                    break;
                case 'en':
                    $product_list[$k]['name'] = $v['name_en'];
                    break;
                case 'zh_ft':
                    $product_list[$k]['name'] = $v['name_zh_ft'];
                    break;
                default :
                    $product_list[$k]['name'] = $v['name_zh'];
                    break;
            }
        }
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Credentials : true");
        return APIFormat::success([
            'total' => $total,
            'profit' => $profit,
            'list' => $product_list,
            'lang' => $lang,
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
        $lang = Yii::$app->request->get('Lang','zh');

        $product = Product::find()->where(['id' => $id])->one();
        switch ($lang){
            case 'zh':
                $product_list['name'] = $product->name_zh;
                break;
            case 'en':
                $product_list['name'] = $product->name_en;
                break;
            case 'zh_ft':
                $product_list['name'] = $product->name_zh_ft;
                break;
            default :
                $product_list['name'] = $product->name_zh;
                break;
        }
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

        return APIFormat::success(
            [
                'product' => $product_list,
                'wallet' => $wallet,
                'amount' => $amount,
                'rate' => $rate,
                'push' => $push,
            ]
        );


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
