<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;


/* @var $this yii\web\View */
/* @var $model backend\models\Setting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group')->textInput(['maxlength' => true]) ?>

    <?php 

        if(!$model->isNewRecord){

            switch ($model->key) {
                case 'modules_estate':
                case 'modules_card':
                case 'init_page':
                    echo FileInput::widget([
                        'name' => 'image',
                        'pluginOptions' => array_merge(Yii::$app->params['FileInput'], [
                            'initialPreview' => $model->isNewRecord ? '' : Yii::$app->params['apiUrl'] . '/' . $model->value,
                        ]),
                        'pluginEvents' => [
                            //选择文件后处理事件
                            'filebatchselected' => "function(event, files){
                                $(this).fileinput('upload');
                            }",
                            //异步上传成功结果处理
                            'fileuploaded' => "function(event, data, id, index){
                                $('#setting-value').val(data.response.image);
                            }",
                        ],
                    ]);
                    echo $form->field($model, 'value')->hiddenInput()->label(false);
                    break;
                case 'protocol_wallet':
                    echo $form->field($model, 'value')->widget(\edofre\ckeditor\CKEditor::className(), [
                        'editorOptions' => [
                            'language' => 'nl',
                        ],
                    ]);
                    break;
                case 'protocol_license':
                    echo $form->field($model, 'value')->widget(\edofre\ckeditor\CKEditor::className(), [
                        'editorOptions' => [
                            'language' => 'nl',
                        ],
                    ]);
                    break;
                case 'protocol_privacy':
                    echo $form->field($model, 'value')->widget(\edofre\ckeditor\CKEditor::className(), [
                        'editorOptions' => [
                            'language' => 'nl',
                        ],
                    ]);
                    break;
                case 'protocol_invitation':
                    echo $form->field($model, 'value')->widget(\edofre\ckeditor\CKEditor::className(), [
                        'editorOptions' => [
                            'language' => 'nl',
                        ],
                    ]);
                    break;
                case 'supernode_rule':
                    echo $form->field($model, 'value')->textarea(['rows' => 6]);
                    break;
                default:
                    echo $form->field($model, 'value')->textInput(['maxlength' => true]);
                    break;
            }

        }

    ?>

    

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
