<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model backend\models\Poster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="poster-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-md-6">
            <label class="control-label">海报图片</label>
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
                        $('#poster-url').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'url')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'sort')->textInput() ?>
            <?= $form->field($model, 'status')->dropDownList($model::statusArray()) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
