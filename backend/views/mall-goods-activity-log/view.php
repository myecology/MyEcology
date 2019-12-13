<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\MallGoodsActivityLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '节点收益', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-goods-activity-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'type',
                'value' => function($model){
                    return \common\models\shop\MallGoodsActivityLog::$typeArr[$model->type];
                },
            ],
            // 'type',
            // 'mall_goods_id',
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
