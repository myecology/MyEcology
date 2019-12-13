<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\MallLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '购买记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            // 'user_id',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'goods_name',
            'number',
            'symbol',
            'amount',
            'remark',
            'order_sn',
            // 'activity',
            // 'activity_name',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return \common\models\shop\MallLog::$statusArr[$model->status];
                },
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
