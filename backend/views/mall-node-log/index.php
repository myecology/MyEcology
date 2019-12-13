<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MallNodeLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '购买记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-node-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'alte_price',
            'alte_symbol',
            // 'alte_status',
            [
                'attribute' => 'alte_status',
                'value' => function($model){
                    $statusArray = \common\models\node\MallNodeLog::$altestatusArr;
                    return $statusArray[$model->alte_status];
                },
                'filter' => \common\models\node\MallNodeLog::$altestatusArr
            ],
            'alte_at',
            'super_price',
            'super_symbol',
            // 'super_status',
            [
                'attribute' => 'super_status',
                'value' => function($model){
                    $statusArray = \common\models\node\MallNodeLog::$superstatusArr;
                    return $statusArray[$model->super_status];
                },
                'filter' => \common\models\node\MallNodeLog::$superstatusArr
            ],
            'super_at',
            'created_at',
            //'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
