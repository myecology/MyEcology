<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;


/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <label class="control-label">头像</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->headimgurl,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                        $(this).fileinput('upload');
                    }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                        $('#user-headimgurl').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'headimgurl')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'upid')->textInput() ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>
            <div class="row">
                <div class="col-md-6"><?= $form->field($model, 'sex')->dropDownList($model::sexArray()) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'age')->textInput() ?></div>
            </div>
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
