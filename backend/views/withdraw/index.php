<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\Models\search\WithdrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    [
//                        'attribute' => 'user.username',
                        'attribute' => 'username',
                        'value' => 'user.username',
                        'label' => '用户名',
                    ],
                    [
                        'attribute' => 'address',
                        'headerOptions' => ['width' => '33%'],
                        'contentOptions' => ['class' => 'contentTd'],
                        'label' => '转出地址',
                    ],
                    [
                        'attribute' => 'symbol',
                        'label' => '转出金额',
                        'value' => function ($model) {
                            return floatval($model->amount)." {$model->symbol}";
                        }
                    ],
                    [
                        'attribute' => 'fee',
                        'label' => '手续费',
                        'value' => function ($model) {
                            return floatval($model->fee)." {$model->fee_symbol}";
                        }
                    ],
                    'created_at:datetime:提交时间',
                    'updated_at:datetime:更新时间',
                    [
                        'attribute' => 'status',
                        'label' => '状态',
                        'value' => function ($model) {
                            return \common\models\Withdraw::$lib_status[$model->status];
                        },
                        'filter' => \common\models\Withdraw::$lib_status
                    ],
//                    'checker_id' => 'Checker ID',
//                    'check_time' => 'Check Time',
//                    'source' => 'Source',
//                    'remark' => 'Remark',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}{success}{fail}',
                        'buttons' => [
                            'success' => function($url, $model, $key){
                                if($model->status == \common\models\Withdraw::STATUS_SUBMITTED) {
                                    return Html::a('发送公链', $url, [
                                        'class' => 'btn btn-success btn-xs',
                                        'data' => [
                                            'confirm' => '是否发送公链',
                                        ]
                                    ]);
                                }
                            },
                            'fail' => function($url, $model, $key){
                                if($model->status == \common\models\Withdraw::STATUS_SUBMITTED) {
                                    return Html::a('退回', $url, [
                                        'class' => 'btn btn-success btn-xs',
                                        'data' => [
                                            'confirm' => '是否确定退回',
                                        ]
                                    ]);
                                }
                            },
                        ]
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
