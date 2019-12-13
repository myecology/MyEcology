<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\node\MallNodeLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mall-node-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'alte_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alte_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'alte_status')->textInput() ?>

    <?= $form->field($model, 'alte_at')->textInput() ?>

    <?= $form->field($model, 'super_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'super_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'super_status')->textInput() ?>

    <?= $form->field($model, 'super_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
