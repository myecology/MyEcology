<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CurrencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '币种列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加币种', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            // 'id',
                            // 'icon',
                            [
                                'attribute' => 'icon',
                                'value' => function($model){
                                    return Html::img($model->icon, ['width' => 50]);
                                },
                                'format' => 'raw',
                            ],
                            'symbol',
                            // 'description',
                            // 'created_at',
                            // 'updated_at',
                            
                            'model',
                            // [
                            //     'attribute' => 'model',
                            //     'value' => function($model){
                            //         $modelArray = $model::$lib_model;
                            //         return $modelArray[$model->model];
                            //     },
                            //     'filter' => $searchModel::$lib_model,
                            // ],
                            
                            'weight',
                            'fee_symbol',
                            'fee_withdraw_amount',
                            //'status',
                            [
                                'attribute' => 'status',
                                'value' => function($model){
                                    $statusArray = $model::$lib_status;
                                    return $statusArray[$model->status];
                                },
                                'filter' => $searchModel::$lib_status,
                            ],
                            'created_at:datetime',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
    </div>

</div>
