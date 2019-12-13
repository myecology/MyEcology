<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\crow\MallCrowLog */

$this->title = 'Update Mall Crow Log: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Crow Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mall-crow-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
