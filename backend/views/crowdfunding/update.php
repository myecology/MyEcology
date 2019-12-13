<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Crowdfunding */

$this->title = '修改众筹: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '众筹列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
?>
<div class="crowdfunding-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
