<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\WineActivationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wine-activation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'activation_code') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?= $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'end_time') ?>

    <?php // echo $form->field($model, 'count') ?>

    <?php // echo $form->field($model, 'grant_count') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'every_amount') ?>

    <?php // echo $form->field($model, 'every_price') ?>

    <?php // echo $form->field($model, 'symbol') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
