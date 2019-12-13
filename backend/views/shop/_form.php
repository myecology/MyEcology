<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;



/* @var $this yii\web\View */
/* @var $model common\models\Shop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shop-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <label class="control-label">营业执照</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->license,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                            $(this).fileinput('upload');
                        }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                            $('#shop-license').val(data.response.image);
                    }"
                ],
            ]);?>
            <?= $form->field($model, 'license')->hiddenInput()->label(false) ?>

            <label class="control-label">店铺照片</label>
            <?= FileInput::widget([
                'name' => 'image',
                'pluginOptions' => array_merge(Yii::$app->params['FileInput'],[
                    'initialPreview' => $model->isNewRecord ? '' : $model->store_photos,
                ]),
                'pluginEvents' => [
                    //选择文件后处理事件
                    'filebatchselected' => "function(event, files){
                            $(this).fileinput('upload');
                        }",
                    //异步上传成功结果处理
                    'fileuploaded' => "function(event, data, id, index){
                            $('#shop-store_photos').val(data.response.image);
                        }"
                ],
            ]);?>
            <?= $form->field($model, 'store_photos')->hiddenInput()->label(false) ?>
        </div>

        <div class="col-md-8">

            <?= $form->field($model, 'userid')->hiddenInput(['value'=>null])->label(false) ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'contact')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'type_id')->dropDownList(ArrayHelper::map(\app\models\ShopType::find()->all(), 'id', 'title')) ?>

            <?= $form->field($model,'province_id')->dropDownList(\common\models\Area::getCityList(),
                [
                    'prompt'=>'--请选择省--',
                    'onchange'=>'
                    $(".form-group.field-member-area").hide();
                    $.post("'.yii::$app->urlManager->createUrl('shop/site').'?typeid=1&pid="+$(this).val(),function(data){
                        $("select#shop-city_id").html(data);
                    });',
            ]) ?>

            <?= $form->field($model, 'city_id')->dropDownList(\common\models\Area::getCityList($model->province_id),
                [
                    'prompt'=>'--请选择市--',
                    'onchange'=>'
                    $(".form-group.field-member-area").show();
                    $.post("'.yii::$app->urlManager->createUrl('shop/site').'?typeid=2&pid="+$(this).val(),function(data){
                        $("select#shop-district_id").html(data);
                    });',
                ]) ?>

            <?= $form->field($model, 'district_id')->dropDownList(\common\models\Area::getCityList($model->city_id),['prompt'=>'--请选择区--',]) ?>

            <?= $form->field($model, 'status')->dropDownList(\common\models\Shop::$lib_status) ?>

            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'introduction')->textarea(['rows' => 6]) ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
