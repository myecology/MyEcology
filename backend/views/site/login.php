<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = '用户登陆';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>MyEcology</b>管理</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">MyEcology后台管理平台</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'code', $fieldOptions2)
                    ->label(false)
                    ->textInput(['placeholder' => '请先输入密码后发送'])?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4 sendBut">
                <button onclick="return sendsms()" class='btn btn-primary btn-block btn-flat' id='smsSend' type="button" > 发送验证码</button>
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

        <!-- <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in
                using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign
                in using Google+</a>
        </div> -->
        <!-- /.social-auth-links -->

        <!-- <a href="#">I forgot my password</a><br> -->
        <!-- <a href="register.html" class="text-center">Register a new membership</a> -->

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
<script src="http://libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script>

    var timeS = 60;
    var timesFun = '';
    var clickState = true;
    function sendsms() {
        if(clickState == false){
            return;
        }
        var username = $('#loginform-username').val();
        var password = $('#loginform-password').val();
        $.ajax({
            type: "post",
            url:'http://api.antc.bxguo.net/v1/user/admin-sms',
            data:{
                username:username,
                password:password
            },
            success: function(data) {
                if(data.status == 200) {
                    alert('发送成功');
                    clickState = false;
                    timeS = 60;
                    setInterval(setTime,1000);
                    return;
                }
                alert(data.msg);
            }, error:function(res){
                console.log(res);
            }
        })
        return false;
    }


    function setTime(){
        console.log(timeS)
        if(timeS <= 0){
            $("#smsSend").text('发送验证码');
            clickState = true;
            clearInterval(timesFun);
            return;
        }

        $("#smsSend").text('倒计时 '+timeS);
        timeS--;
    }
</script>