<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Currency;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyPrice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-price-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(Currency::find()->all(), 'id', 'symbol')) ?>

            <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'source')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'poundage')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'is_exchange')->dropDownList([0=>'不允许',1=>"允许"]) ?>
        </div>
        <div class="col-md-3"></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
