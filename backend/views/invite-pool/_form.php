<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Currency;
use kartik\widgets\FileInput;
use kartik\widgets\DateTimePicker;



/* @var $this yii\web\View */
/* @var $model backend\models\InvitePool */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invite-pool-form">

    <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-4">

                <label class="control-label">项目图标</label>
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
                            $('#invitepool-icon').val(data.response.image);
                        }"
                    ],
                ]);?>
                <?= $form->field($model, 'icon')->hiddenInput()->label(false) ?>
                <label class="control-label">项目海报</label>
                <?= FileInput::widget([
                    'name' => 'image',
                    'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                        'initialPreview' => $model->isNewRecord ? '' : $model->background,
                    ]),
                    'pluginEvents' => [
                        //选择文件后处理事件
                        'filebatchselected' => "function(event, files){
                            $(this).fileinput('upload');
                        }",
                        //异步上传成功结果处理
                        'fileuploaded' => "function(event, data, id, index){
                            $('#invitepool-background').val(data.response.image);
                        }"
                    ],
                ]);?>
                <?= $form->field($model, 'background')->hiddenInput()->label(false) ?>

            </div>
            <div class="col-md-8">
                <?php if($model->isNewRecord){ ?>
                <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>
                <?php } ?>
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <div class="row">
                    <div class="col-md-6"><?= $form->field($model, 'currency_id')->dropDownList(ArrayHelper::map(Currency::loadAvailable(), 'id', 'symbol')) ?></div>
                    <div class="col-md-6"><?= $form->field($model, 'type')->dropDownList($model::typeArray()) ?></div>
                    <div class="col-md-6"><?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-md-6"><?= $form->field($model, 'prize')->textInput(['maxlength' => true]) ?></div>
                </div>
                <div class="row">
                    <div class="col-md-3"><?= $form->field($model, 'prize_registerer')->textInput() ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'prize_inviter')->textInput() ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'prize_grand_inviter')->textInput() ?></div>
                    <div class="col-md-3"><?= $form->field($model, 'prize_grand_grand_inviter')->textInput() ?></div>
                </div>
                <?= $form->field($model, 'expired_at')->widget(DateTimePicker::classname(),[
                    'type' => DateTimePicker::TYPE_INPUT,
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-mm-dd hh:ii:ss'
                    ]
                ]) ?>
                <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
                <?= $form->field($model, 'status')->dropDownList($model::statusArray()) ?>
            </div>
        </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
