<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Fill */

$this->title = 'Update Fill: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fills', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fill-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
