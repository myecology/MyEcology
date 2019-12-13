<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index table-responsive">

    <div class="box box-success">
        <div class="box-body">

                <p>
                    <?= Html::a('添加用户', ['create'], ['class' => 'btn btn-success']) ?>
                </p>
                

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            // ['class' => 'yii\grid\SerialColumn'],

                            'id',
                            // 'upid',
                            // 'area',
                            // 'initials',
                            [
                                'attribute' => 'headimgurl',
                                'value' => function($model){
                                    return Html::img($model->headimgurl, ['width' => 50]);
                                },
                                'format' => 'raw',
                            ],
                            'username',
                            'nickname',
                            //'iecid',
                            'userid',
                            // 'headimgurl',
                            //'access_token',
                            //'auth_key',
                            //'password_hash',
                            //'password_reset_token',
                            //'email:email',
                            //'longitude',
                            //'latitude',
                            //'sex',
                            //'age',
                            //'country',
                            //'province',
                            //'city',
                            // 'status',
                            'code',
                            //'description',
                            'created_at:datetime',
                            //'updated_at',
                            //'friend',
                            //'payment_hash',
                            //'is_iec',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{view}{blacklist}{cancel-black-list}',
                                'headerOptions' => ['width' => 120],
                                
                                'buttons' => [
                                    'view' => function($url, $model, $key){
                                        return Html::a('详情', $url, ['title' => '详情', 'class' => 'btn btn-info btn-xs']);
                                    },
                                    'blacklist' => function($url, $model, $key){
                                        if($model->status == 10 ){
                                            return Html::a('冻结帐号', $url, [
                                                'class' => 'btn btn-warning btn-xs',
                                                'data' => [
                                                    'confirm' => '是否冻结帐号',
                                                ]
                                            ]);
                                        }
                                    },
                                    'cancel-black-list' => function($url, $model, $key){
                                        if($model->status == 0 ){
                                            return Html::a('解冻帐号', $url, [
                                                'class' => 'btn btn-success btn-xs',
                                                'data' => [
                                                    'confirm' => '是否解冻帐号',
                                                ]
                                            ]);
                                        }
                                    },
                                    // 'wallet' => function($url, $model, $key){
                                    //     return Html::a('钱包', $url, ['title' => '钱包', 'class' => 'btn btn-success btn-xs']);
                                    // }
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
    </div>

</div>
