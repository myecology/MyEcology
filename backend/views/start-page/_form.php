<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\StartPage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="start-page-form">
<!--    ['options' => ['enctype' => 'multipart/form-data']]-->
    <?php $form = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]); ?>


    <div class="row">
        <div class="col-md-4">
            <label class="control-label">启动页图片</label>
            <?= FileInput::widget([
                'name' => 'image',
                'options' => ['multiple' => false],
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->img,
                    'minFileCount' => 1,
                    //最多上传的文件个数限制
                    'maxFileCount' => 1,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                        $(this).fileinput('upload');
                    }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                        $('#startpage-img').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'img')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'redirecturl')->textInput(['maxlength' => true]) ?>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'type')->dropDownList($model::typeArray()) ?>
</div>
                <div class="col-md-4"><?= $form->field($model, 'sort')->textInput() ?>
</div>
                <div class="col-md-4"><?= $form->field($model, 'time')->textInput() ?>
</div>
            </div>
                
                <?= $form->field($model, 'status')->dropDownList($model::statusArray()) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
