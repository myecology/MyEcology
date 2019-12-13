<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use common\models\Crowdfunding;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Crowdfunding */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crowdfunding-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <label class="control-label">众筹图片</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->income_img,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                        $(this).fileinput('upload');
                    }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                        $('#crowdfunding-income_img').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'income_img')->hiddenInput()->label(false) ?>

        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'start_time')->widget(DateTimePicker::classname(),['options'=>['placeholder'=>''],'pluginOptions'=>['autoclose'=>true]]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'end_time')->widget(DateTimePicker::classname(),['options'=>['placeholder'=>''],'pluginOptions'=>['autoclose'=>true]]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'status')->dropDownList(Crowdfunding::$statusArr) ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'release_type')->dropDownList(Crowdfunding::$typeArr) ?></div>

                <div class="col-md-4"><?= $form->field($model, 'release_start_at')->widget(DateTimePicker::classname(),['options'=>['placeholder'=>''],'pluginOptions'=>['autoclose'=>true]]) ?></div>

                <div class="col-md-4"><?= $form->field($model, 'release_end_at')->widget(DateTimePicker::classname(),['options'=>['placeholder'=>''],'pluginOptions'=>['autoclose'=>true]]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'release_cycle')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'mall_symbol')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'mall_proportion')->textInput(['maxlength' => true]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-3"><?= $form->field($model, 'exchange_symbol')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-3"><?= $form->field($model, 'exchange_num')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-3"><?= $form->field($model, 'min_buy')->textInput(['maxlength' => true]) ?></div>

                <div class="col-md-3"><?= $form->field($model, 'exchange_total')->textInput(['maxlength' => true]) ?></div>
            </div>
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'brief_introduction')->textarea(['maxlength' => true,'rows' => 6]) ?>
        </div>
    </div>
    
    <!-- <div class="form-group" id='aa'>
           说是
       </div>  -->   

    <div class="form-group" id='aaa'>
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <script>
        setTimeout(function(){
$("#aa").click(function(){
            var timeS = new Date($("#crowdfunding-start_time").val());

            if(timeS == 'Invalid Date'){
                console.log('萨达奥所多')
            }else{
                $("console.log.btn-success").click();
            }
        })
        },1000)

    </script>
</div>
