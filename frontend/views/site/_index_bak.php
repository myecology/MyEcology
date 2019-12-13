<?php

use yii\bootstrap\ActiveForm;

// AppAsset::register($this);
frontend\assets\AppAsset::addScript($this, '/js/gt.js');
?>
<style>
    .mask2,.mask3{
        position:absolute;
        top:0;
        left:0;
        z-index: 10;
        background-color:rgba(0,0,0,.5);
    }
    .mask3{
        background-color: #f2f2f2;
    }
    .bg{
        width:100%;
    }
    .mask2K{
        width:90%;
        color: #ebcd9a;
        font-size:16px;
        text-align:center;
        line-height:30px;
    }
    .mask2K p:nth-child(4){
        font-size:22px;
        margin-top:7%;
    }
    .run,.run2{
        margin:auto;
    }
    .boxK{
        width: 34%;
        left:0;
        right:0;
        bottom:16%;
        margin:auto;
    }
    .boxK img{
        width:100%;
    }
    .LogoImg{
        width:15%;
        display:block;
        margin:auto;
        margin-top:13%;
        margin-bottom:5%;
    }
    .run2{
        display:none;
    }
    .msT{
        width:100%;
    }
    .zi{
        font-size:18px;
        color: #fcebc1;
        width:100%;
    }
    .kuang{
        text-align:center;
        position:absolute;
        top:10%;
        width:100%;
        left:0;
    }
    .kuang img{
        width:54px;
        margin-bottom:10px;
    }
    .jin{
        font-size:52px;
        font-weight:bold;
        height:30px;
        line-height:30px;
        margin-top:16%;
        margin-bottom:10%;
        text-align:center;
    }
    .hintZ{
        color:#ada8a8;
        /*text-align:center*/;
        width:100%;
    }
    .hint img{
        height:36px;
    }
    .tishi{
        line-height:40px;
        color:#000;
        font-size:28px;
        margin-left:5px;
    }
    .hint{
        height: 20px;
        margin: auto;
        width: 200px;
        padding-left:56px;
    }
    .hintZ{
        margin-top:5px;
        font-size:16px;
    }
    .nextBtn{
        color:#fff;
        text-align:center;
        line-height:36px;
        height: 36px;
        border-radius:5px;
        background-color: #0084f2;
        position:absolute;
        left:0;
        right:0;
        bottom:10%;
        width:90%;
        margin:auto;
        font-size:14px;
        letter-spacing:1px;
    }
    .linkDownload{
        display: block;
        text-align: center;
        color: #999;
        font-size: 14px;
        text-decoration: underline;
    }

    #embed-captcha {
            width: 300px;
            margin: 0 auto;
        }
        .show {
            display: block;
        }
        .hide {
            display: none;
        }
        #notice {
            color: red;
            text-align: center;
        }
        .inp {
            border: 1px solid gray;
            padding: 0 10px;
            width: 200px;
            height: 30px;
            font-size: 18px;
        }
        .area{color:#0084f2;line-height:32px;height:32px;border:none;}
        .yzmlf{border-left:1px solid #0084f2;padding-left:8px;height:26px;line-height:26px;margin-top:3px;color:#0084f2;}
</style>

<link rel="stylesheet" href="/css/index.css">
<div class="main">
    <img src="/images/head.png" class="head" alt="">
    <?php $form = ActiveForm::begin([
        'id'     => 'signup',
        'action' => ['/site/signup']
    ])?>

    <div class="content">
        <div class="title"></div>
        <div class="cl mes">
            <div class="z imgBox pr">
                <select name="area" class="area" id="area">
                    <option value="86"> +86 </option>
                </select>
            </div>
            <input class="z phone" name="SignupForm[username]" type="text" placeholder="请输入手机号码">
        </div>
        <div id="embed-captcha" style="width:100%;"></div>
        <p id="wait" class="show">正在加载验证码......</p>
        <p id="notice" class="hide">请先点击按钮验证再获取验证码</p>
        <br>

        <div class="cl mes">
            <div class="z imgBox pr">
                <img src="/images/icon_2.png" class="paAuto" alt="">
            </div>
            <input class="z code" name="SignupForm[code]" type="text" placeholder="请输入短信验证码">
            <div class="y yzmlf" id="yzmFun">获取验证码</div>
        </div>
        
        <div class="cl mes">
            <div class="z imgBox pr">
                <img src="/images/icon_3.png" class="paAuto" alt="">
            </div>
            <input class="z password" name="SignupForm[password]" type="password" placeholder="密码最少8位，大写或小写字母+数字组合">
        </div>
        <div class="cl mes">
            <div class="z imgBox pr">
                <img src="/images/icon_3.png" class="paAuto" alt="">
            </div>
            <input class="z password2" type="password" placeholder="请再次输入密码" style="text-align:left;" />
        </div>
        <div class="cl mes">
            <div class="z imgBox pr">
                <img src="/images/icon_4.png" class="paAuto" alt="">
            </div>
            <div class="z yzmInput" style="margin-top:7px;"><?= $inviteCode ?></div>
            <input type="hidden" name="SignupForm[inviteCode]" id="inviteCode" value="<?= $inviteCode ?>" style="text-align:center;" />
        </div>

        <?php ActiveForm::end()?>

        <?php $form = ActiveForm::begin([
            'id'     => 'sms',
            'action' => ['/site/sms']
        ])?>

        <?= $form->field($sms, 'verifyCode')->hiddenInput()->label(false); ?>
        <?= $form->field($sms, 'phone')->hiddenInput()->label(false); ?>
        <?= $form->field($sms, 'type')->hiddenInput(['value' => 1])->label(false); ?>
        <div id="embed-captcha-geetest"></div>
        <div class="cl sub" id="submit">
            立即注册
        </div>

        <br><br>
        <div>
            <a href="https://app.antc.bxguo.net" class="linkDownload">暂不注册或已注册，直接下载</a>
        </div>
    </div>
    <?php ActiveForm::end()?>
</div>
<div class="mask none">
    <div class="maskMain paAuto" style="z-index:1000">
        <!-- <div class="close pa" onclick="closeFun()">
            <img src="/images/close.png" class="paAuto" alt="">
        </div> -->
        <div class="maskTitle">
            注册成功
        </div>
        <div class="maskHint">
            恭喜你获得<span> <?= $invitePool->prize*$invitePool->prize_registerer/10 ?> <?= $invitePool->symbol ?></span>, 下载APP获得更多<?= $invitePool->symbol ?>！
        </div>
        <a href="https://app.antc.bxguo.net">
            立即下载
        </a>
    </div>
</div>
<div class="mask2 " style="z-index:1000">
    <div class="mask2K paAuto" style="background-image:url('/images/img_1.png');background-size:100% 100%;background-repeat:no-repeat;">
        <!-- <img src="/images/img_2.jpg" class="LogoImg" alt=""> -->
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <div class="boxK pa" id="open-wallet">
            <img src="/images/img_3.png" class="run paAuto" alt="">
        </div>
    </div>
</div>
<div class="mask3 none" style="z-index:1000">
    <img src="/images/img_5.png" class="msT" alt="">
    <div class="kuang">
        <!-- <img src="/images/img_2.jpg" class="logo_2" alt=""> -->
        <div class="zi">&nbsp;</div>
    </div>
    <div class="jin"><?= $invitePool->prize*$invitePool->prize_registerer/10 ?> <?= $invitePool->symbol ?></div>
    <div class="hint">
<!--        <img src="/images/img_6.png" class="z" alt="">-->
        <div class="z hintZ">已放入钱包</div>
    </div>
    <div class="nextBtn" id="receive">
        立即领取
    </div>
</div>


<?php

$csrf = Yii::$app->request->getCsrfToken();

$js = <<<JS
var time = 60;
var yzmInt = '';
var yzmState = true;


var handlerEmbed = function (captchaObj) {
    $("#yzmFun").click(function (e) {
        var validate = captchaObj.getValidate();
        if (!validate) {
            $("#notice")[0].className = "show";
            setTimeout(function () {
                $("#notice")[0].className = "hide";
            }, 2000);
            e.preventDefault();
            return ;
        }

        var phone = $(".phone").val();
        var reg = /^1[3|4|5|6|7|8|9][0-9]\d{8}$/;
        if (!reg.test(phone)){
            layer.open({
                content: '请输入正确的手机号'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            });
            return
        }
        if(yzmState){

            var \$form = $('#sms');
            var \$html = $('#embed-captcha').find('.geetest_form').html();
            $('#embed-captcha-geetest').html(\$html);
            $.ajax({ 
                url  : \$form.attr('action'), 
                type  : 'post', 
                dataType : 'json',
                data  : \$form.serialize(), 
                success: function (response){ 
                    if(response.status == 200){
                        $(".yzm").text(time).addClass("yzmNoClick");
                        yzmInt = setInterval(timeFun,1000);
                        yzmState = false;
                            layer.open({
                            content: '短信验证码发送成功'
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        }); 
                    }else if(response.msg){
                        layer.open({
                            content: response.msg
                            ,skin: 'msg'
                            ,time: 2 //2秒后自动关闭
                        });
                    }
                }, 
                error : function (ev){ 
                    layer.open({
                        content: '网络异常'
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    }); 
                }
            }); 
        }else{
            layer.open({
                content: '请稍后再点'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            });
        }
    });
    // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
    captchaObj.appendTo("#embed-captcha");
    captchaObj.onReady(function () {
        $("#wait")[0].className = "hide";
    });
};

$.ajax({
    // 获取id，challenge，success（是否启用failback）
    url: "/geetest?t=" + (new Date()).getTime(), // 加随机数防止缓存
    type: "get",
    dataType: "json",
    success: function (data) {
        console.log(data);
        // 使用initGeetest接口
        // 参数1：配置参数
        // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
        $('#smsform-verifycode').val(data.success);
        initGeetest({
            gt: data.gt,
            challenge: data.challenge,
            new_captcha: data.new_captcha,
            product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
            offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
            // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
        }, handlerEmbed);
    }
});

$('.phone').blur(function(ev){
    $('#smsform-phone').val($(this).val());
});

$(".mask,.mask2,.mask3").css({
    height:$(window).height(),
    width:$(window).width()
});
$(".boxK").css({
    height:$(window).width()*0.34
});
$(".mask2K").css({
    height:$(window).width()*0.9*741/573
});


function timeFun(){
    time--;
    if(time < 0){
        $(".yzm").text('获取验证码').removeClass("yzmNoClick");
        clearInterval(yzmInt);
        time = 60;
        yzmState = true;
    }else{
        $(".yzm").text(time);
    }
}

$('#closeFun').click(function(){
    $(".mask").hide();
});

$('#submit').click(function(){
    var phone = $(".phone").val();
    var code = $(".code").val();
    var pass_1 = $(".password").val();
    var pass_2 = $(".password2").val();
    var inviteCode = $("#inviteCode").val();

    var reg = /^1[3|4|5|6|7|8|9][0-9]\d{8}$/;
    if (!reg.test(phone)){
        layer.open({
            content: '请输入正确的手机号'
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
        return
    }
    if(!inviteCode){
        layer.open({
            content: '邀请码无效'
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
        return
    }
    if (!code){
        layer.open({
            content: '请输入验证码'
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
        return
    }
    if (pass_1 != pass_2){
        layer.open({
            content: '两次密码不一致'
            ,skin: 'msg'
            ,time: 2 //2秒后自动关闭
        });
        return
    }

    var \$form = $('#signup');
    $.ajax({ 
        url  : \$form.attr('action'), 
        type  : 'post', 
        dataType : 'json',
        data  : \$form.serialize(), 
        success: function (response){ 
            if(response.status == 200){
                $(".mask").show();
            }else{
                layer.open({
                    content: response.msg
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
            }
        }, 
        error : function (ev){ 
            layer.open({
                content: '网络异常'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            }); 
        }
    }); 
})


$('#open-wallet').click(function(){
    var  appendTemp = '<img src="/images/img_4.gif" class="run2 paAuto" alt="">';
    $(".boxK").append(appendTemp);
    
    $(".run").hide();
    $(".run2").show();
    setTimeout(function(){
        $(".mask2").fadeOut();
        $(".mask3").fadeIn()
    },1500);
})

$('#receive').click(function(){
    $(".mask3").hide();
})

JS;
$this->registerJs($js); ?>
