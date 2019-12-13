<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Official */

$this->title = '修改通知: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Officials', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="official-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
