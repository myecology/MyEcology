<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\WineActivationLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '活动记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-activation-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            // 'activation_id',
            [
                'attribute' => 'activation_id',
                'value' => function($model){
                    return \common\models\shop\WineActivationLog::$typeArr[$model->activation_id];
                },
            ],
            // 'user_id',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'amount',
            'symbol',
            'created_at',
        ],
    ]) ?>

</div>
