<?php

namespace frontend\modules\bank\controllers;

use Yii;
use yii\web\Controller;
use common\models\bank\Order;
use common\models\bank\Profit;
use common\models\bank\Log;

/**
 * Default controller for the `bank` module
 */
class UserController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        //  锁仓订单
        $list['lock'] = Order::find()->where(['status' => Order::STATUS_LOCK, 'uid' => Yii::$app->user->identity->id])->orderBy('id desc')->all();

        //  解锁订单
        $list['unlock'] = Order::find()->where([
            'AND',
            ['>=', 'status', Order::STATUS_PROFIT],
            ['=', 'uid', Yii::$app->user->identity->id],
        ])->orderBy('id desc')->all();


        return $this->renderPartial('index', [
            'list' => $list
        ]);
    }

    //  收益明细
    public function actionProfit()
    {
        $data = Order::find()->where(['uid' => Yii::$app->user->identity->id])->orderBy('id desc')->limit(100)->all();

        $profitTotal = Profit::find()->where(['uid' => Yii::$app->user->identity->id,'type'=>0])->sum('amount');

        return $this->renderPartial('profit', [
            'data' => $data,
            'profitTotal' => $profitTotal
        ]);
    }

    //  交易记录
    public function actionLog()
    {
        $data = Log::find()->where(['uid' => Yii::$app->user->identity->id])->orderBy('id desc')->limit(100)->all();

        return $this->renderPartial('log', [
            'data' => $data
        ]);
    }
}
