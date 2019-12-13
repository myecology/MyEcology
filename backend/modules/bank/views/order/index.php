<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\bank\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <form action="/bank/order/download-excel" method="get" >
                开始时间：<input type="date" name="start_time">
                结束时间：<input type="date" name="end_time">
                选择状态：<select name="status">
                    <option value ="all"></option>
                    <?php foreach(\common\models\bank\Order::statusArray()  as $k => $v) { ?>
                        <option  value ="<?php echo $k ?>"><?php echo $v ?></option>
                   <?php }?>
                </select>

                <input type="submit" value="下载excel">
            </form>
            <br>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                             'id',
                            // 'uid',
                            [
                                'label' => '用户名',
                                'attribute' => 'username',
                                'value' => 'user.username'
                            ],
                            [
                                'label' => '产品名称',
                                'attribute' => 'productName',
                                'value' => 'product.name'
                            ],
                            // 'product_id',
                            'rate',
                            'amount',
                            // 'status',
                            
                            'day',
                            //'endtime:datetime',
                            'created_at:datetime',
                            [
                                'attribute' => 'status',
                                'value' => function($model){
                                    $statusArray = $model::statusArray();
                                    return $statusArray[$model->status];
                                },
                                'filter' => $searchModel::statusArray(),
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{back}{reissued}',
                                'buttons' => [
                                    'back' => function($url, $model, $key){
                                        if($model->status == $model::STATUS_LOCK){
                                            return Html::a('退回', $url, [
                                                'class' => 'btn btn-warning btn-xs',
                                                'data' => [
                                                    'confirm' => '是否确认退回用户理财',
                                                ]
                                            ]);
                                        }
                                    },
                                    'reissued' => function($url, $model, $key){
                                        if($model->status == \common\models\bank\Order::STATUS_PROFIT ||$model->status == \common\models\bank\Order::STATUS_END ){
                                            return Html::a('补发收益', $url, [
                                                'class' => 'btn btn-warning btn-xs',
                                            ]);
                                        }
                                    }
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
    </div>

</div>
