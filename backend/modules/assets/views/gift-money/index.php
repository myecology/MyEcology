<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\assets\models\search\GiftMoneySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '红包记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-money-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            // 'id',
                            [
                                'label' => '发送人',
                                'attribute' => 'sendUsername',
                                'value' => 'user.username',
                            ],
                            'symbol',
                            // 'sender_id',
                            'amount',
                            'amount_left',
                            // 'status',
                            // 'type',
                            
                            'amount_unit',
                            'count',
                            //'expired_at',
                            'description',
                            //'bind_taker',
                            [
                                'attribute' => 'status',
                                'value' => function($model){
                                    return $model::$lib_status[$model->status];
                                },
                                'filter' => $searchModel::$lib_status
                            ],
                            [
                                'attribute' => 'type',
                                'value' => function($model){
                                    return $model::$lib_type[$model->type];
                                },
                                'filter' => $searchModel::$lib_type
                            ],
                            'created_at:datetime',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}'
                            ],
                        ],
                    ]); ?>
                </div>
    </div>

</div>
