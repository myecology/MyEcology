<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MallCrowLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '购买记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-crow-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            // 'crow_id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'crow_name',
            'number',
            'pay_symbol',
            'symbol',
            'pay_number',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\crow\MallCrowLog::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\crow\MallCrowLog::$statusArr
            ],
            // 'type',
            [
                'attribute' => 'type',
                'value' => function($model){
                    $statusArray = \common\models\crow\MallCrowLog::$typeArr;
                    return $statusArray[$model->type];
                },
                'filter' => \common\models\crow\MallCrowLog::$typeArr
            ],
            'release_at',
            'release_cycle',
            'release_times',
            'each_release',
            'created_at',
            // 'release_end_at',
            // 'mall_crow_id',
            // 'is_release',
            [
                'attribute' => 'is_release',
                'value' => function($model){
                    $statusArray = \common\models\crow\MallCrowLog::$releaseArr;
                    return $statusArray[$model->is_release];
                },
                'filter' => \common\models\crow\MallCrowLog::$releaseArr
            ],
            'time',
            // 'flag',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
