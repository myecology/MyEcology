<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\node\NodeWt */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="node-wt-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4">

            <label class="control-label">节点图片</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->income,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                        $(this).fileinput('upload');
                    }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                        $('#nodewt-income').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'income')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'alte_price')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'alte_symbol')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'alte_number')->textInput(['maxlength' => true]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'total_awards')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'reward_symbol')->textInput(['maxlength' => true]) ?></div>
            </div>

            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'super_number')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'super_factor')->textInput(['maxlength' => true]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'super_price')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'super_symbol')->textInput(['maxlength' => true]) ?></div>
            </div>
            <?= $form->field($model, 'super_explain')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'super_rules')->textarea(['rows' => 6]) ?>
            <?= $form->field($model, 'alte_rules')->textarea(['maxlength' => true,'rows' => 6]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
