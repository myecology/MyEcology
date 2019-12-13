<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CrowdfundingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '众筹列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crowdfunding-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建众筹', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            // 'income_img',
            'start_time',
            'end_time',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\Crowdfunding::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\Crowdfunding::$statusArr
            ],
            // 'release_type',
            [
                'attribute' => 'release_type',
                'value' => function($model){
                    $statusArray = \common\models\Crowdfunding::$typeArr;
                    return $statusArray[$model->release_type];
                },
                'filter' => \common\models\Crowdfunding::$typeArr
            ],
            // 'release_start_at',
            // 'release_end_at',
            // 'release_cycle',
            // 'mall_symbol',
            // 'mall_proportion',
            // 'exchange_symbol',
            // 'exchange_num',
            //'created_at',
            //'update_at',
            // 'min_buy',
            // 'exchange_total',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
