<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/6/21
 * Time: 3:13 PM
 */

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\OrderAdd;
use common\models\bank\Log;
use common\models\bank\Order;
use common\models\bank\Product;
use common\models\bank\Profit;
use common\models\CurrencyPrice;
use common\models\Wallet;

class OrderController extends BaseController
{
    public function actionIndex(){

        try {
            //  累积收益
            $profit['total'] = Profit::find()->where(['uid' => \Yii::$app->user->identity->id])->sum('amount');

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
                ['=', 'uid', \Yii::$app->user->identity->id],
            ])->sum('amount');
            $result = APIFormat::success($profit);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * 理财列表
     * @return array|void
     */
    public function actionProductList(){
        try {
            //  理财列表
            $list = Product::find()->where(['status' => Product::STATUS_ACTIVE])->orderBy('id desc')->all();

            $result = APIFormat::success($list);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * 理财详情
     * @return array|void
     */
    public function actionProductView(){
        try {
            $product = Product::findOne(\Yii::$app->request->post('id'));
            $percent = Product::findOne(\Yii::$app->request->post('percent'));

            $wallet = Wallet::find()
                ->where(['user_id' => \Yii::$app->user->identity->id, 'symbol' => $product->symbol])
                ->one();

            //  投注额度
            //$amount = Order::find()->where(['product_id' => $product->id])->sum('amount');

            $price = CurrencyPrice::findOne(['symbol' => $product->symbol]);

            //  默认收益
            $userAmount = $wallet['amount'] - $wallet['amount_lock'];
            $push['amount'] = $userAmount * empty($percent)? 0.25 :(float)$percent;
            $push['profit'] = (($push['amount'] * $product->rate * $product->currency_price / 100) / $product->earn_currency_price);
            $list = [
                'product' => $product,
                'push' => $push,
            ];
            $result = APIFormat::success($list);
        } catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }
        return $result;
    }


    public function actionAddOrder(){
        $transaction = \Yii::$app->db->beginTransaction();
        $result = false;
        try {
            $model = new OrderAdd();
            if ($model->load(\Yii::$app->request->post()) && false !== $model->orderAdd()) {
                $transaction->commit();
                $result = APIFormat::success();
            }

        } catch (\ErrorException $e) {
            $transaction->rollBack();
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }
        return $result;
    }


    public function actionOrderLockList(){
        $result = false;
        try{
            $user_id = \Yii::$app->user->identity->id;
            $result = Order::lockOrderList($user_id);
            $result = APIFormat::success($result);
        }catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }
        return $result;
    }
    public function actionOrderUnlockList(){
        $result = false;
        try{
            $user_id = \Yii::$app->user->identity->id;
            $result = Order::lockOrderList($user_id);
            $result = APIFormat::success($result);
        }catch (\ErrorException $e) {
            $result = APIFormat::error($e->getCode(), $e->getMessage());
        }
        return $result;
    }


}