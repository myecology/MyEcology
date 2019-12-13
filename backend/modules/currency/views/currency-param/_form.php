<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Currency;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyParam */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-param-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <?= $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(Currency::find()->all(), 'id', 'symbol')) ?>

            <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
