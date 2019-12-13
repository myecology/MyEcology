<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\bank\search\ProductSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'symbol') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'rate') ?>

    <?php // echo $form->field($model, 'min_amount') ?>

    <?php // echo $form->field($model, 'max_amount') ?>

    <?php // echo $form->field($model, 'income_id') ?>

    <?php // echo $form->field($model, 'income_description') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'fee') ?>

    <?php // echo $form->field($model, 'fee_explain') ?>

    <?php // echo $form->field($model, 'day') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'statime') ?>

    <?php // echo $form->field($model, 'endtime') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
