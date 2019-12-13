<?php 
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>我的锁仓</title>
    <meta name = "format-detection" content = "telephone=no">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/css/modules-bank/common.css">
    <link rel="stylesheet" href="/css/modules-bank/my.css">
</head>
<body>
    <div class="main">
        <div class="mainTop cl">
            <a href="<?= Url::to(['user/profit']) ?>" class="z">
                <img src="/images/modules-bank/icon_1.png" alt="">
                <p>收益明细</p>
            </a>
<!--            <a href="javascript:void(0)" class="z">-->
<!--                <img src="/images/modules-bank/icon_2.png" alt="">-->
<!--                <p>理财钱包</p>-->
<!--            </a>-->
            <a href="<?= Url::to(['user/log']) ?>" class="z" style="float: right;">
                <img src="/images/modules-bank/icon_3.png" alt="">
                <p>交易记录</p>
            </a>
        </div>
        <div class="ti cl pr">
            <img src="/images/modules-bank/st2_2.png" class="pa mak_1" alt="">
            <img src="/images/modules-bank/st2_2.png" class="pa mak_2 none" alt="">
            <div class="z selectD pr" onclick="selectFun(0)">
                已锁仓令牌
            </div>
            <div class="y pr" onclick="selectFun(1)">
                已解锁令牌
            </div>
        </div>
        <div class="boxAll">
            <div class="state_1 cl">
                <?php foreach($list['lock'] as $val){ ?>
                    <div class="box">
                        <div class="boxTitle">
                            <?= $val->product->name ?>
                        </div>
                        <div class="boxMes cl">
                            <div class="mes z">
                                <p class="stateColor"><?= sprintf("%.2f", (($val->rate * $val->amount * $val->currency_price / 100) / $val->earn_currency_price)) ?></p>
                                <p>预计每日收益(<?= \common\models\bank\Product::getEarnSymbol($val->product_id)?>)</p>
                            </div>
                            <div class="mes y">
                                <p class="stateColor"><?= sprintf("%.8f", $val->amount) ?></p>
                                <p>已锁仓(<?= $val->symbol?>)</p>
                            </div>
                            <div class="mes z">
                                <p class="stateColor">
                                    <?php
                                        $s = time() - $val->created_at;
                                        $v = (int)($s / 86400);
                                        echo $val->day - $v;
                                    ?>
                                天</p>
                                <p>剩余天数</p>
                            </div>
                            <div class="mes y">
                                <p class="stateColor">锁仓的时间</p>
                                <p><?= date('Y/m/d H:i', $val->created_at) ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="state_2 cl none">

                <?php foreach($list['unlock'] as $val){ ?>
                    <div class="box">
                        <div class="boxTitle">
                            <?= $val->product->name ?>
                        </div>
                        <div class="boxMes cl">
                            <div class="mes z">
                                <?php
                                    $amount = sprintf("%.2f", $val->amount);
                                    if($val->status == \common\models\bank\Order::STATUS_END){
                                        $walletAmount = $amount . '/' . $amount;
                                    }else{
                                        $income = $val->product->income;
                                        if($income->type == \common\models\bank\Income::TYPE_FUNDS_FEE){
                                            $cun = \common\models\bank\Profit::find()->where(['order_id' => $val->id, 'uid' => $val->uid, 'product_id' => $val->product_id])->count();
                                            $avg = sprintf("%.2f", $val->amount / ($val->day / $income->day) * $cun);
                                            $walletAmount = $avg . '/' . $amount;
                                        }else{
                                            $walletAmount = '0.00' . '/' . $amount;
                                        }
                                    }
                                ?>
                                <p class="stateColor"><?= $walletAmount ?></p>
                                <p>已放入钱包(<?= $val->symbol?>)</p>
                            </div>
                            <?php $left = 0; foreach($val->profit as $v){
                                if ($v->type == 0) {
                                    $left += $v['amount'];
                                }
                            }
                            ?>
                            <div class="mes y">
                                <p class="stateColor"><?= $left ?></p>
                                <p>已释放收益(<?= $val->earn_symbol?>)</p>
                            </div>
                            <div class="mes y">
                                <p class="stateColor">解锁的时间</p>
                                <p><?= date('Y/m/d H:i', $val->created_at + ($val->day + 1 + ($val->product->income->day * $val->product->income->num)) * 86400) ?></p>
                            </div>
                            <div class="mes z">
                                <p class="stateColor">
                                    <?php
                                    $profitRight = sprintf("%.2f", (($val->rate * $val->amount * $val->currency_price / 100) / $val->earn_currency_price) * $val->day);
//                                    $profitLeft = $profitRight - sprintf("%.2f",$left);
                                    echo $profitRight;
                                    ?>
                                </p>
                                <p>预计总收益</p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
<script src="/js/jquery-2.2.2.min.js"></script>
<script>
    function selectFun(index){
        $(".boxAll > div").hide();
        $(".boxAll > div").eq(index).show();
        $(".ti img").hide();
        $(".ti img").eq(index).show();
        $(".ti div").removeClass('selectD');
        $(".ti div").eq(index).addClass('selectD');
    }
</script>
</html>