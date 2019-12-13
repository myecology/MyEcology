<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WineLevelLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '邀请关系';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-level-log-index">

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

            [
                'label' => '邀请人',
                'attribute' => 'inviterUsername',
                'value' => 'inviterUser.username',
            ],
            // 'pid',
            'created_at:datetime',
            // 'number',
            'goods_name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
