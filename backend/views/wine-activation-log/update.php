<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\shop\WineActivationLog */

$this->title = 'Update Wine Activation Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Wine Activation Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="wine-activation-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
