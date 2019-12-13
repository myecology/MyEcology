<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = '登陆';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
p{
    font-size: 12px;
}
</style>
<link rel="stylesheet" href="/css/login.css">
<bmr-root _nghost-c0="" ng-version="4.3.5"><router-outlet _ngcontent-c0=""></router-outlet><bmr-login _nghost-c11=""><router-outlet _ngcontent-c11=""></router-outlet><bmr-signin _nghost-c12=""><div _ngcontent-c12="" class="page-container">
    <bmr-login-top-navigation _ngcontent-c12="" altmessagetype="signup" _nghost-c13=""><div _ngcontent-c13="" class="nav-elements-container row align-items-center">
    <div _ngcontent-c13="" class="col-2">
        <a _ngcontent-c13="" class="logo-container" routerlink="/" href="/">
            <img src="/images/_logo.png" id="logo" alt="logo" width="140" height="30">
        </a>
    </div>
    <div _ngcontent-c13="" class="col-10 text-right">

        <!----><p _ngcontent-c13="" class="alt-message">没有账号？
            <a _ngcontent-c13="" class="bmr-link" href="<?=Url::to(['signup'])?>">立即注册</a>
        </p>
        <!---->

    </div>
</div>
</bmr-login-top-navigation>

    <div _ngcontent-c12="" class="centered-content">
        <div _ngcontent-c12="" class="centered">
            <h2 _ngcontent-c12="" class="text-center">比特贷用户登陆</h2>


            <?php $form = ActiveForm::begin()?>

                <?=$form->field($model, 'phone')->textInput(['placeholder' => '输入手机号码'])?>



                <?= $form->field($model,'verifyCode')->label(false)
                ->widget(\dungang\geetest\widgets\Captcha::className(),[
                    'platform'=>'pc',  //默认是pc ，还可以设置为mobile 移动端
                    'captchaId'=>'geetest', //极验证码的id 
                    'clientOptions'=>[
                        'submitButton'=>'#submit', //绑定表单的提交按钮
                        'showType'=>'float' //验证码的展现形式，之支持pc端，可选值：embed,float,popup
                    ],
                ])?>



                <small _ngcontent-c12="" class="form-text text-muted" style="font-size: 12px">忘记密码了吗?
                    <a _ngcontent-c12="" class="bmr-link" routerlink="" href="javascript:;">重设密码</a>
                </small>

                <button _ngcontent-c12="" class="bmr-button" type="submit">登陆</button>

            <?php ActiveForm::end()?>

            <hr _ngcontent-c12="">

            <a _ngcontent-c12="" href="https://blog.beamery.com/beamery-pages/" target="_blank">
                <div _ngcontent-c12="" class="blog-post-preview">
                    <p _ngcontent-c12="" class="title">🎉 全球最大的区块链数字货币资产平台</p>
                    <div _ngcontent-c12="" class="blog-post-preview-footer">
                        <div _ngcontent-c12="" class="date">May 30, 2018</div>
                        <a _ngcontent-c12="" class="bmr-link" href="javascript:;" target="_blank">Read more</a>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
</bmr-signin>
</bmr-login>
</bmr-root>
