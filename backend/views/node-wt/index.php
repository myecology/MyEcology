<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\NodeWtSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '节点列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-wt-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Node Wt', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            // 'income',
            'alte_price',
            'alte_symbol',
            'alte_number',
            // 'alte_rules:ntext',
            'total_awards',
            'reward_symbol',
            'super_number',
            'super_factor',
            //'super_rules:ntext',
            'super_price',
            //'super_explain:ntext',
            'created_at',
            //'update_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
