<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ExchangeWt */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'WT兑换列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exchange-wt-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'e_symbol',
            'amount',
            'wt_number',
            'create_time',
            'symbol_price',
            'fee',
        ],
    ]) ?>

</div>
