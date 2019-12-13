<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\WineLevel */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '直推人数', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-level-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'level',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'effective_time:datetime',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return \common\models\shop\WineLevel::$statusArr[$model->status];
                },
            ],
            // 'status',
        ],
    ]) ?>

</div>
