<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;


/* @var $this yii\web\View */
/* @var $model common\models\Currency */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="currency-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-md-6">
            <label class="control-label">币种Icon</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->icon,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                        $(this).fileinput('upload');
                    }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                        $('#currency-icon').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'icon')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-6">

            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-6"><?= $form->field($model, 'fee_symbol')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'fee_withdraw_amount')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'weight')->textInput() ?></div>
                <div class="col-md-6"><?= $form->field($model, 'status')->dropDownList($model::$lib_status) ?></div>
            </div>

            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
        </div>
    </div>


    

    


    

    

    

    

    

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
