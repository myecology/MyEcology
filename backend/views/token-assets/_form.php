<?php

use common\models\bank\Income;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TokenAssets */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="token-asstes-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-1"></div>

            <div class="col-md-10">
                <div class="row tb">
                    <div class="col-md-4"><?= $form->field($model, 'token_type_id')->dropDownList(ArrayHelper::map(\common\models\RcTokenType::find()->all(), 'id', 'name')) ?> </div>
                    <div class="col-md-4"><?= $form->field($model, 'personnel_type')->textInput(['maxlength' => true]) ?></div>

                    <div class="col-md-4"><?= $form->field($model, 'type')->dropDownList($model::typeArray()) ?> </div>
                    <div class="col-md-3"><?= $form->field($model, 'remark')->textInput(['maxlength' => true])?> </div>

                    <div class="col-md-3"><?= $form->field($model, 'currency_total')->textInput(['maxlength' => true]) ?></div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'start_time')->widget(DateTimePicker::classname(),[
                            'type' => DateTimePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'startDate' => date('Y-m-d',time()),
                                'todayHighlight' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'end_time')->widget(DateTimePicker::classname(),[
                            'type' => DateTimePicker::TYPE_INPUT,
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'startDate' => date('Y-m-d',time()),
                                'todayHighlight' => true,
                                'format' => 'yyyy-mm-dd hh:ii:ss'
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-4 release_cycle" style="display: none"><?= $form->field($model, 'release_cycle')->textInput() ?> </div>
                    <div class="col-md-4 every_time_number" style="display: none"><?= $form->field($model, 'every_time_number')->textInput() ?> </div>

<!--                    分阶段释放时可以开启-->
                    <div class="col-md-3" style="display: none"><?= $form->field($model, 'stage_data')->textInput() ?> </div>

                </div>
                <div class="form-group">
<!--                    <input type="button" class="btn btn-success"  value="保存" onClick="add()">-->
                    <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
                </div>

            </div>
            <div class="col-md-1"></div>
        </div>


    <?php ActiveForm::end(); ?>
    <script>
       setTimeout(function(){
           $("#tokenassets-currency_total," +
               "#tokenassets-start_time," +
               "#tokenassets-end_time," +
               "#tokenassets-release_cycle").bind('change',function(){
               keyChange();
           });
           function getDayTime(day) {
                let _day = new Date(day);
                return _day.getTime() / 1000;
           }
           function keyChange(){
               let start_time = getDayTime($("#tokenassets-start_time").val());
               let end_time = getDayTime($("#tokenassets-end_time").val());
               let currency_total = $("#tokenassets-currency_total").val();
               let release_cycle = $("#tokenassets-release_cycle").val();
               let every_time_number = $('#tokenassets-every_time_number');
               if(start_time && end_time && currency_total && release_cycle){
                   if(end_time < start_time){
                       alert('时间设置不合理');
                       return;
                   }
                   let time = Math.ceil((end_time - start_time) / (release_cycle * 86400))+1 ;
                   let v = currency_total / time;
                   every_time_number.val(v.toFixed(4));
               }

           }

           $("#tokenassets-type").on('change',function(){
               var type = $("#tokenassets-type").val();
               if(type == 2){
                   // $('.hid').show()
                   $('.release_cycle').show();
                   $('.every_time_number').show();
               }else{
                   $('.release_cycle').hide();
                   $('.every_time_number').hide();
                   $('.release_cycle').val(0);
                   $('.every_time_number').val(0);
                   // $('.hid').hide();
                   // $(".a").not(":eq(0)").remove();
                   // $(".b").not(":eq(0)").remove();
                   // $(".c").not(":eq(0)").remove();
                   // timeD = 1;
               }
           })
       },1000)
    </script>
</div>
