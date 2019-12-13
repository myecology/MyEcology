<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WineActivationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '激活列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-activation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'activation_code',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            // 'updated_at',
            'created_at',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\shop\WineActivation::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\shop\WineActivation::$statusArr
            ],
            //'type',
            'end_time',
            'count',
            'grant_count',
            //'price',
            'every_amount',
            'symbol_price',
            //'every_price',
            'symbol',
            [
                'label' => '挖矿数量',
                'attribute' => 'symbol_number',
                'value' => function($model){
                    
                    return $model->grant_count*$model->every_amount;
                },
            ],

            [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                    ],
        ],
    ]); ?>
</div>
