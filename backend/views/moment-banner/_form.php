<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;


/* @var $this yii\web\View */
/* @var $model backend\models\MomentBanner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="moment-banner-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <label class="control-label">Banner图片</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->url,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                        $(this).fileinput('upload');
                    }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                        $('#momentbanner-url').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'url')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'status')->dropDownList($model::statusArray()) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
