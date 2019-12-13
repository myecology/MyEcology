<?php


use common\models\TokenAssets;
use kartik\widgets\DateTimePicker;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$dataProvider = [];
$searchModel = null;
$this->title = '指定用户';
$this->params['breadcrumbs'][] = ['label' => '代币设置', 'url' => ['index']];

/* @var $this yii\web\View */
/* @var $model common\models\TokenAssets */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="token-asstes-form">
    <h1><?= Html::encode($this->title) ?></h1>
    <span class="info" >代币类型：<?= $token_info->tokenType->name ?></span>
    <span class="info" >发放方式：<?= TokenAssets::typeArray()[$token_info->type] ?></span>
    <span class="info" >备注：<?= $token_info->remark ?></span>
    <div class="row">
        <div class="col-md-12">
            <div class="row tb">
                <div class="col-md-5" >
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data','class'=>'excel']]) ?>
                    <?php $form->action = '/token-assets/read-excel' ?>
                    <?= $form->field($upload_model,'assets_id')->textInput(['value'=>$token_assets_id,'class'=>'hid']);?>
                    <?= $form->field($upload_model, 'inputFile')->fileInput(['class'=>'btn'])->label("批量指定") ?>
                    <button class="btn btn-success" style="height:34px;">批量导入</button>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
    <?php $form = ActiveForm::begin();?>
    <div class="row">
        <div class="col-md-12">
            <div class="row tb">
                <div class="col-md-5"><?= $form->field($model, 'username')->textInput()  ?> </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <h3>查询结果如下</h3>
                <div class="col-md-12" id="users">
                    <?= $form->field($model, 'items[]')->checkboxList([]);  ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <h3>已选中的用户手机号</h3>
            </div>
            <div class="show-phone cl" >
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <div class="row cl">
        <input type="button" class="btn btn-success floatRight" style="margin-right: 5%;margin-top: 150px" onclick="Sub()" value="提交">
    </div>



    <script>
        setTimeout(function(){
            $('#appointuser-username').on('input',function () {
                var phone = $('#appointuser-username').val()
                $.post('/token-assets/get-user-phone',{'username':phone},function(res){
                    $("#appointuser-items label").remove();
                    let users = res.users;
                    if(users){
                        for(var i = 0;i<users.length;i++){
                            var str = '<label><input type="checkbox" onclick="addUser(this)" name="check-user" value="'+users[i].id+'" phone="'+users[i].username+'"> <span> '+ users[i].username +'</span></label>'
                            $("#appointuser-items").append(str);
                        }
                    }
                })
            })
        },1000);
        var checked_users_phone = [];
        function addUser(obj){
            let phone = $(obj).attr('phone');
            let index_phone = checked_users_phone.indexOf(phone);
            if(index_phone === -1){
                checked_users_phone.push(phone);
            }else{
                checked_users_phone.splice(index_phone,1)
            }
            showPhones(checked_users_phone);
        }
        function showPhones(phones){
            $('.show-phone .tb').remove();
            for(i in phones){
                var str ='<div class="tb floatLeft" style="margin-right: 20px">' +
                    '    <span>'+ phones[i] +'</span>' +
                    '    <input type="button" value="删除" phone="'+phones[i]+'" onclick="delFun(this)">' +
                    '</div>';
                $('.show-phone').append(str);
            }
        }
        function delFun(obj){
            let phone = $(obj).attr('phone');
            let index_phone = checked_users_phone.indexOf(phone);
            checked_users_phone.splice(index_phone,1);
            $(obj).parent().remove();
        }
        function Sub(){
            if(!checked_users_phone.length){
                alert('请指定用户');
                return ;
            }
            $.post('/token-assets/save-user',{'checked_users_phone':checked_users_phone,'token_assets_id':<?=  $token_assets_id ?>},(res)=>{
                if(res.code == 444){
                    alert(res.message);
                    return ;
                }
            });
        }
    </script>
    <style >

        .info{
            color: red;
            font-size: 20px;
            margin-right: 30px;
        }
        .cl {
            zoom: 1;
        }
        .cl {
            display: inline-block;
            display: block;
        }
        .f16{
            color:#333;
        }
        .floatLeft {
            float: left;
        }

        .floatRight{
            float: right;
        }
        .excel{
            display: flex;
            align-items: center
        }
        .hid{
            display: none;
        }
    </style>
</div>
