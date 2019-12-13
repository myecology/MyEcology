<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>令牌理财</title>
    <meta name = "format-detection" content = "telephone=no">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/css/modules-bank/common.css">
    <link rel="stylesheet" href="/css/modules-bank/licai.css">
</head>
<body>
<div class="main">
    <div class="mainTop pr">
        <div class="moneyCount pr">
            <div class="money cl">
                <p class="dw"><?= sprintf("%.2f", $profit['total']) ?></p>
                <P>≈￥<?= sprintf("%.2f", $total['rmb']) ?></P>
            </div>
            <div class="z zr">
                <p>昨日增益</p>
                <p class="stateColor"><?= sprintf("%.2f", $profit['lastday']) ?: 0 ?>ANT</p>
            </div>
            <div class="z lj">
                <p>累计收益</p>
                <p class="stateColor"><?= sprintf("%.2f", $profit['total']) ?: 0 ?>ANT</p>
            </div>
        </div>
        <img src="/images/modules-bank/st2_2.png" class="pa mak" alt="">
        <a href="<?= Url::to(['user/index']) ?>" class="myC pa">
            我的锁仓
        </a>
    </div>

    <?php foreach($list as $product){ ?>
        <a href="<?= Url::to(['info', 'id' => $product['id']]) ?>">
            <div class="box">
                <div class="title pr">
                    <?= $product->name ?>
                </div>
                <div class="mesBox cl">
                    <div class="mes z">
                        <div class="stateColor2"><?= sprintf("%.2f", $product->rate) ?>%</div>
                        <div>每日收益率</div>
                    </div>
                    <div class="mes y">
                        <div class="stateColor2"><?= ($product->rate * $product->currency_price * 100 / 100) / $product->earn_currency_price?></div>
                        <div>100枚预计收益(<?= $product->earn_symbol?>)</div>
                    </div>
                </div>
            </div>
        </a>
    <?php } ?>
  
</div>
<div class="txMask none">
    <div class="box2 paAuto cl">
        <div class="boxT cl">可提现到区块钱包</div>
        <div class="boxMoney cl">1000IEC</div>
        <div class="boxHint cl">不可提现  500IEC</div>
        <div class="cancal z" onclick="cancelFun()">
            取消
        </div>
        <div class="sure y" onclick="sureFun()">
            立即提现
        </div>
    </div>
</div>
</body>
<script src="/js/jquery-2.2.2.min.js"></script>
<script>
    function txFun(){
        $(".txMask").show();
    }
    function cancelFun(){
        $(".txMask").hide();
    }
    function sureFun(){
        $(".txMask").hide();
    }
</script>
</html>