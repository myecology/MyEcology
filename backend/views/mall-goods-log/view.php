<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\MallGoodsLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '购买记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-goods-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'goods_name',
            'order_sn',
            'number',
            // 'order_goods_id',
            'activity_name',
            // 'activity',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return \common\models\shop\MallGoodsLog::$statusArr[$model->status];
                },
            ],
            'created_at',
            'updated_at',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'amount',
            'symbol',
        ],
    ]) ?>

</div>
