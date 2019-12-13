<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\bank\Income;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\bank\Product */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            
            <div class="row">
                <?= '<div class="col-md-12">' . $form->field($model, 'name')->textInput(['maxlength' => true]) . '</div>'; ?>
                <div class="col-md-6"><?= $form->field($model, 'status')->dropDownList($model::statusArray()) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'income_id')->dropDownList(ArrayHelper::map(Income::find()->all(), 'id', 'name')) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'symbol')->dropDownList(ArrayHelper::map(\common\models\Currency::loadAvailable(), 'symbol', 'symbol')) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'currency_price')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'earn_currency_id')->dropDownList(ArrayHelper::map(\common\models\Currency::loadAvailable(), 'id', 'symbol')) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'earn_currency_price')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'day')->textInput() ?></div>
                <div class="col-md-6"><?= $form->field($model, 'max_amount')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'user_amount')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'min_amount')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'rate')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6">
                    <?= $form->field($model, 'statime')->widget(DateTimePicker::classname(),[
                        'type' => DateTimePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd hh:ii:ss'
                        ]
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'endtime')->widget(DateTimePicker::classname(),[
                        'type' => DateTimePicker::TYPE_INPUT,
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-mm-dd hh:ii:ss'
                        ]
                    ]) ?>
                </div>
            </div>

            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
            <div class="form-group">
                <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <div class="col-md-3"></div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
