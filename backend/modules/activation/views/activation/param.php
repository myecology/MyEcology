<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\activation\Activation */

$this->title = '设置参数' . $activation->name;
$this->params['breadcrumbs'][] = ['label' => '活动列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $activation->name, 'url' => ['view', 'id' => $activation->id]];
$this->params['breadcrumbs'][] = 'param';
?>
<div class="activation-update">
    <div class="row">
        <div class="col-md-3 typeModel echoSg">
            当前模式为：<span><?php if(empty($model->mode)||$model->mode ==\backend\modules\activation\models\ActivationParamForm::MODE_ROYALTY):?>提成模式<?php else:?>等级模式<?php endif;?></span>
        </div>
        <div class="col-md-3">
            <?php if(empty($model->mode)||$model->mode ==\backend\modules\activation\models\ActivationParamForm::MODE_ROYALTY):?>
                <?= Html::button('切换为等级模式', ['class' => 'btn btn-success changeButton','type' => 'button','dataType' => 'level_div_from','onclick'=>'disFrom(this)']) ?>
            <?php else:?>
                <?= Html::button('切换为提成模式', ['class' => 'btn btn-success changeButton','type' => 'button','dataType' => 'royalty_div_from','onclick'=>'disFrom(this)']) ?>
            <?php endif;?>

        </div>
    </div>
    <div class="row" id ='royalty_div_from' style="margin-left: -0px; display: <?php if(empty($model->mode)||$model->mode ==\backend\modules\activation\models\ActivationParamForm::MODE_ROYALTY):?>block;<?php else:?>none;<?php endif;?>">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row reward_level_from">
            <div class="col-md-6">
                <?= $form->field($model, 'mode')->hiddenInput(['value'=>\backend\modules\activation\models\ActivationParamForm::MODE_ROYALTY])->label(false) ?>

                <?= $form->field($model, 'reward_symbol')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Currency::find()->all(), 'symbol', 'symbol'))?>

                <?= $form->field($model, 'royalty_type')->dropDownList(\backend\modules\activation\models\ActivationParamForm::$royaltyTypeArr) ?>

                <?= $form->field($model, 'royalty')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 royalty" id="royalty" >
                <?php if(empty($model->reward_level)): ?>
                    <div class="row reward_level">
                        <div class="col-md-5">
                            <?= $form->field($model, 'reward_level[0][key]')->textInput(['maxlength' => true])->label('第几等级') ?>
                        </div>
                        <div class="col-md-5">
                            <?= $form->field($model, 'reward_level[0][value]')->textInput(['maxlength' => true])->label('数值') ?>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label"  for ="" ></label>
                            <p id=""><?= Html::button('删除', ['class' => 'btn btn-danger','type' => 'button','onclick'=>'del(this)']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($model->reward_level as $key => $value) :?>
                        <div class="row reward_level">
                            <div class="col-md-5">
                                <?= $form->field($model, "reward_level[$key][key]")->textInput(['maxlength' => true,'value' => $value['key'] ])->label('第几等级') ?>
                            </div>
                            <div class="col-md-5">
                                <?= $form->field($model, "reward_level[$key][value]")->textInput(['maxlength' => true,'value' => $value['value']])->label('数值') ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label"  for ="" ></label>
                                <p id=""><?= Html::button('删除', ['class' => 'btn btn-danger','type' => 'button','onclick'=>'del(this)']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="row add-but-royalty">
                    <div class="col-md-5">
                        <?= Html::button('添加', ['class' => 'btn btn-success','type' => 'button','dataType' => 'add-royalty','onclick'=>'add(this)']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="row" id = 'level_div_from' style="margin-left: -0px; display: <?php if($model->mode ==\backend\modules\activation\models\ActivationParamForm::MODE_LEVEL):?>block;<?php else:?>none;<?php endif;?>">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row reward_level_from">
            <div class="col-md-6">
                <?= $form->field($model, 'mode')->hiddenInput(['value'=>\backend\modules\activation\models\ActivationParamForm::MODE_LEVEL])->label(false) ?>

                <?= $form->field($model, 'reward_symbol')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Currency::find()->all(), 'symbol', 'symbol'))?>

                <?= $form->field($model, 'level_proportion')->textInput() ?>

                <?= $form->field($model, 'expiration_time')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6 royalty" id="royalty">
                <?php if(empty($model->reward_level)): ?>
                    <div class="row reward_level">
                        <div class="col-md-5">
                            <?= $form->field($model, 'reward_level[0][key]')->textInput(['maxlength' => true])->label('第几等级') ?>
                        </div>
                        <div class="col-md-5">
                            <?= $form->field($model, 'reward_level[0][value]')->textInput(['maxlength' => true])->label('数值') ?>
                        </div>
                        <div class="col-md-2">
                            <label class="control-label"  for ="" ></label>
                            <p id=""><?= Html::button('删除', ['class' => 'btn btn-danger','type' => 'button','onclick'=>'del(this)']) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($model->reward_level as $key => $value) :?>
                        <div class="row reward_level">
                            <div class="col-md-5">
                                <?= $form->field($model, "reward_level[$key][key]")->textInput(['maxlength' => true,'value' => $value['key'] ])->label('第几等级') ?>
                            </div>
                            <div class="col-md-5">
                                <?= $form->field($model, "reward_level[$key][value]")->textInput(['maxlength' => true,'value' => $value['value']])->label('数值') ?>
                            </div>
                            <div class="col-md-2">
                                <label class="control-label"  for ="" ></label>
                                <p id=""><?= Html::button('删除', ['class' => 'btn btn-danger','type' => 'button','onclick'=>'del(this)']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="row add-but-level">
                    <div class="col-md-5">
                        <?= Html::button('添加', ['class' => 'btn btn-success','type' => 'button','dataType' => 'add-level','onclick'=>'add(this)']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<div class="row reward_level rewardAddTemp" style="display:none;">
    <div class="col-md-5">
        <?= $form->field($model, 'reward_level[0][key]')->textInput(['maxlength' => true,'class'=>'form-control key'])->label('第几等级') ?>
    </div>
    <div class="col-md-5">
        <?= $form->field($model, 'reward_level[0][value]')->textInput(['maxlength' => true,'class'=>'form-control value'])->label('数值') ?>
    </div>
    <div class="col-md-2">
        <label class="control-label"  for ="" ></label>
        <p id=""><?= Html::button('删除', ['class' => 'btn btn-danger ','type' => 'button','onclick'=>'del(this)']) ?></p>
    </div>
</div>
<style>
    .echoSg{
        font-weight: bold;
        font-size:20px;
    }
</style>
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script>

    var beforeClickTime = 0;


    function add(obj){

        var a = Date.parse(new Date());

        if(a == beforeClickTime){
            return;
        }else{
            beforeClickTime = a;
        }

        var temp = $('.rewardAddTemp').eq(0).clone().show().removeClass('rewardAddTemp');

        // temp.find('.key')

        temp.find('.value').attr('name','ActivationParamForm[reward_level]['+a+'][value]').val('');
        temp.find('.key').attr('name','ActivationParamForm[reward_level]['+a+'][key]').val('');


        if($(obj).attr('dataType') == 'add-royalty'){
            $(".add-but-royalty").before(temp);
        }else{
            $(".add-but-level").before(temp);
        }
    }

    function del(obj){
        if($('#royalty .btn-danger').length<=1){
            alert("buneng")
        }else{
            $(obj).parent().parent().parent().remove();
        }

    }

    function disFrom(obj){
        $('#level_div_from,#royalty_div_from').hide();
        switch ($(obj).attr('dataType')) {
            case 'royalty_div_from':
                $("#royalty_div_from").show();
                $(obj).attr('dataType','level_div_from').text('切换为等级模式');
                $(".typeModel span").text('提成模式')
                break;
            case 'level_div_from':
                $("#level_div_from").show();
                $(obj).attr('dataType','royalty_div_from').text('切换为提成模式');
                $(".typeModel span").text('等级模式')
                break
        }
    }
</script>
