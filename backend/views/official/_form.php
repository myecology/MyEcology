<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Official */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="official-form">

    <?php $form = ActiveForm::begin(); ?>
    <?=$form->errorSummary($model) ?>
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
        <?= $form->field($model, 'subtitle')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'display')->dropDownList([0 =>  '不显示', 1 =>  '显示']) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
