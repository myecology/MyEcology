<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\shop\MallGoodsLog */

$this->title = 'Update Mall Goods Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Goods Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mall-goods-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
