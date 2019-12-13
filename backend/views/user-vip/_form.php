<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\shop\UserVip */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-vip-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'proportion')->textInput() ?>

    <?= $form->field($model, 'valid')->dropDownList($model::$valid_zh) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
