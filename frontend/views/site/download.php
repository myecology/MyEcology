<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title class="wcTitle"></title>
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no"/>
    <link rel="stylesheet" href="/css/common.css">
    <style>
        body{
            background-color:#2eb7df;
        }
        .logo{
            display:block;
            height: 120px;
            width:200px;
            top:-20%;
        }
        .logo img{
            width:65px;
            display: block;
            margin:auto;
            margin-bottom:14px;
        }
        .logo div{
            color:#fff;
            text-align:center;
            font-size:22px;
            letter-spacing:2px;
            font-weight:bold;
        }
        .box{
            height: 50px;
            border: 1px solid #fff;
            border-radius: 10px;
            color: #fff;
            display: block;
            width: 75%;
            left: 0;
            right: 0;
            margin: auto;
        }
        .box img{
            height: 28px;
            margin:10px 0px;
        }
        .name{
            height:48px;
            font-size:18px;
            line-height:48px;
            width:100px;
            text-align:center;
        }
        .lv{
            font-size:18px;
            top: 0;
            left:0;
            margin:auto;
            bottom:0;
            height:48px;
            line-height:48px;
            width:100px;
            text-align:right;
        }
        .ios{
            bottom:50px;
        }
        .an{
            bottom:130px;
        }
        .box a{
            opacity: 0;
            position:absolute;
            top: 0;
            left: 0;
            width:100%;
            height: 100%;
            display: block;
        }
        .kuang{
            height:48px;
            width:90%;
        }
    </style>
</head>
<body>
<div class="main">
    <div class="logo paAuto">
        <img src="/images/logo.png" alt="">
        <div>区块恋上你</div>
    </div>
    <div class="box pa ios">
        <div class="kuang paAuto">
            <img src="/images/smallIcon_1.png" class="z " alt="ios">
            <div class="name z"><?= $type[$ios->type] ?></div>
            <div class="lv z">V <?= $ios->version ?></div>
        </div>
        <a href="<?= $ios->url ?>"></a>
    </div>
    <div class="box pa an">
        <div class="kuang paAuto">
            <img src="/images/smallIcon_2.png" class="z " alt="android">
            <div class="name z"><?= $type[$android->type] ?></div>
            <div class="lv z">V <?= $android->version ?></div>
        </div>
        <a href="<?= $android->url ?>"></a>
    </div>
</div>
</body>
<script src="/js/jquery-2.2.2.min.js"></script>
<script>
    $(".main").css({
        height:$(window).height(),
        width:$(window).width()
    })
</script>
</html>