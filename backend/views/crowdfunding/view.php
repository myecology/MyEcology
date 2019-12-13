<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Crowdfunding */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '众筹列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="crowdfunding-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'income_img',
            'start_time',
            'end_time',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return \common\models\Crowdfunding::$statusArr[$model->status];
                },
            ],
            // 'release_type',
            [
                'attribute' => 'release_type',
                'value' => function($model){
                    return \common\models\Crowdfunding::$typeArr[$model->release_type];
                },
            ],
            'release_start_at',
            'release_end_at',
            'release_cycle',
            'mall_symbol',
            'mall_proportion',
            'exchange_symbol',
            'exchange_num',
            'created_at',
            'update_at',
            'min_buy',
            'exchange_total',
            'remark',
            'brief_introduction',
        ],
    ]) ?>

</div>
