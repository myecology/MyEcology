<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\WineActivation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '挖矿收益', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-activation-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'activation_code',
            'user_id',
            'updated_at',
            'created_at',
            'status',
            'type',
            'end_time',
            'count',
            'grant_count',
            'price',
            'every_amount',
            'every_price',
            'symbol',
        ],
    ]) ?>

</div>
