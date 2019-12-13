<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\MallNodeLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mall-node-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'alte_price') ?>

    <?= $form->field($model, 'alte_symbol') ?>

    <?= $form->field($model, 'alte_status') ?>

    <?php // echo $form->field($model, 'alte_at') ?>

    <?php // echo $form->field($model, 'super_price') ?>

    <?php // echo $form->field($model, 'super_symbol') ?>

    <?php // echo $form->field($model, 'super_status') ?>

    <?php // echo $form->field($model, 'super_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
