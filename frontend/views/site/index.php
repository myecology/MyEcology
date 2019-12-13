<?php

use yii\bootstrap\ActiveForm;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title class="wcTitle"></title>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no"/>
    <link rel="stylesheet" href="/css/common.css">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/layer_mobile/need/layer.css">
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
            background-color: #1fb922;
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
    </style>
</head>
<body>
    <div class="main">
        <img src="/images/head.png" class="head" alt="">
        <?php $form = ActiveForm::begin()?>

        <div class="content">
            <div class="title">手机注册</div>
            <div class="cl mes">
                <div class="z imgBox pr">
                    <img src="/images/icon_1.png" class="paAuto" alt="">
                </div>
                <input class="z phone" name="username" type="text" placeholder="请输入手机号码">
            </div>
            <div class="cl mes">
                <div class="z imgBox pr">
                    <img src="/images/icon_2.png" class="paAuto" alt="">
                </div>
                <input class="z code" name="code" type="text" placeholder="请输入短信验证码">
                <div class="yzm y" onclick="yzmFun()">获取验证码</div>
            </div>
            <div class="cl mes">
                <div class="z imgBox pr">
                    <img src="/images/icon_2.png" class="paAuto" alt="">
                </div>
                <input class="z code" type="text" id="code2" placeholder="请输入图片验证码">
                <?= $form->field($model,'verifyCode')->label(false)
                ->widget(\dungang\geetest\widgets\Captcha::className(),[
                    'clientOptions'=>[
                        'submitButton'=>'#submit',
                        'showType'=>'float'
                    ],

                ])?>
            </div>
            <div class="cl mes">
                <div class="z imgBox pr">
                    <img src="/images/icon_3.png" class="paAuto" alt="">
                </div>
                <input class="z password" type="password" placeholder="请输入密码">
            </div>
            <div class="cl mes">
                <div class="z imgBox pr">
                    <img src="/images/icon_3.png" class="paAuto" alt="">
                </div>
                <input class="z password2" type="password" placeholder="请再次输入密码">
            </div>
            <div class="cl mes">
                <div class="z imgBox pr">
                    <img src="/images/icon_4.png" class="paAuto" alt="">
                </div>
                <div class="z yzmInput"><?= $inviteCode ?></div>
                <input type="hidden" id="inviteCode" value="<?= $inviteCode ?>">
            </div>
            <div class="cl sub" onclick="sub()">
                立即注册
            </div>

            <br><br>
            <div>
                <!-- <a href="/site/download" class="linkDownload">暂不注册或已注册，直接下载</a> -->
            </div>
        </div>
        <?php ActiveForm::end()?>
    </div>
    <div class="mask none">
        <div class="maskMain paAuto">
            <!-- <div class="close pa" onclick="closeFun()">
                <img src="/images/close.png" class="paAuto" alt="">
            </div> -->
            <div class="maskTitle">
                注册成功
            </div>
            <div class="maskHint">
                恭喜你获得<span> <?= $invitePool->prize*$invitePool->prize_registerer/10 ?> <?= $invitePool->symbol ?></span>, 下载APP获得更多<?= $invitePool->symbol ?>！
            </div>
            <a href="/site/download">
                立即下载
            </a>
        </div>
    </div>

</body>
<?php

    $csrf = Yii::$app->request->getCsrfToken();

?>

<script src="/js/jquery-2.2.2.min.js"></script>
<script src="/css/layer_mobile/layer.js"></script>
<script>

    $(".mask,.mask2,.mask3").css({
        height:$(window).height(),
        width:$(window).width()
    })
    $(".boxK").css({
        height:$(window).width()*0.34
    })
    $(".mask2K").css({
        height:$(window).width()*0.9*741/573
    })
    
        var time = 60;
    var yzmInt = '';
    var yzmState = true;
    function yzmFun(){
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
            $.post('/site/sms', {"phone" : phone, "type" : 1, "_csrf-frontend" : "<?= $csrf ?>"}, function(data){
                if(data.status == 200){
                    $(".yzm").text(time).addClass("yzmNoClick");
                    yzmInt = setInterval(timeFun,1000);
                    yzmState = false;
                }else if(data.msg){
                    layer.open({
                        content: data.msg
                        ,skin: 'msg'
                        ,time: 2 //2秒后自动关闭
                    });
                }
            }, 'json');
        }else{
            layer.open({
                content: '请稍后再点'
                ,skin: 'msg'
                ,time: 2 //2秒后自动关闭
            });
        }
    }

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

    function closeFun(){
        $(".mask").hide();
    }

    function sub(){
        var code2 = $('#code2').val();
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

        $.post('/site/signup', {"username" : phone, "code" : code, "password" : pass_1, "inviteCode" : inviteCode, "_csrf-frontend" : "<?= $csrf ?>", "code2" : code2}, function(data){
            if(data.status == 200){
                $(".mask").show();
            }else{
                    layer.open({
                    content: data.msg
                    ,skin: 'msg'
                    ,time: 2 //2秒后自动关闭
                });
                window . setTimeout(changeVerifyCode, 1000);
            }
        }, 'json');
    }

    function runFun(){
        var  appendTemp = '<img src="/images/img_4.gif" class="run2 paAuto" alt="">';
        $(".boxK").append(appendTemp);
        
        $(".run").hide();
        $(".run2").show();
        setTimeout(function(){
            $(".mask2").fadeOut();
            $(".mask3").fadeIn()
        },1500);
    }

    function nextFun(){
        $(".mask3").hide();
    }



    //更换验证码
function changeVerifyCode()
{
    $.ajax({
        url : '/site/captcha?refresh',
        dataType : 'JSON',
        cache : false,
        success : function(data){
            $('#captchaimg').attr('src', data['url']);
            $('body').data('/site/captcha?refresh', [data['hash1'], data['hash2']]);
        }
    })
}

$('#captchaimg').click(function(){
    changeVerifyCode();
})

</script>
</html>
