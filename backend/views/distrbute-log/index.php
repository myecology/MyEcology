<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\DistrbuteLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '通证列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distrbute-log-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'release_symbol',
            'have_symbol',
            'total_amount',
            // 'release_num',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\DistrbuteLog::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\DistrbuteLog::$statusArr
            ],
            'release_amount',
            'created_at',
            //'updated_at',

            // ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
