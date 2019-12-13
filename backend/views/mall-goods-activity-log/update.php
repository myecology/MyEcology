<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\shop\MallGoodsActivityLog */

$this->title = 'Update Mall Goods Activity Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Goods Activity Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mall-goods-activity-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
