<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WineLevelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '直推人数';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-level-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'level',
            // 'user_id',
            'effective_time:datetime',
            'created_at:datetime',
            //'updated_at:datetime',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\shop\WineLevel::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\shop\WineLevel::$statusArr
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
