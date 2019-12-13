<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DistrbuteLog */

$this->title = 'Update Distrbute Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Distrbute Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="distrbute-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
