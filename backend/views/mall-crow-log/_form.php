<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\crow\MallCrowLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mall-crow-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'crow_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'crow_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput() ?>

    <?= $form->field($model, 'pay_symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'release_at')->textInput() ?>

    <?= $form->field($model, 'release_cycle')->textInput() ?>

    <?= $form->field($model, 'release_times')->textInput() ?>

    <?= $form->field($model, 'each_release')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'mall_crow_id')->textInput() ?>

    <?= $form->field($model, 'is_release')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
