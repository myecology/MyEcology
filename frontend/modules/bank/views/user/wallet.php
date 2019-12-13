<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>理财钱包</title>
    <meta name = "format-detection" content = "telephone=no">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/moenyBag.css">
</head>
<body>
    <div class="main">
        <div class="mainTop pr">
            <div class="moneyCount paAuto">
                <p>132132</p>
                累计收益(￥)
            </div>
        </div>
        <div class="boxAll">
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="title cl">
                    <div class="titleText z">ETH</div>
                </div>
                <div class="boxMes cl pr">
                    <div class="mes z">
                        <div class="money stateColor">132132</div>
                        <div class="moenyState cl">
                            <p class="z">可用:100</p>
                            <p class="z">锁仓:100</p>
                        </div>
                    </div>
                    <img src="../img/st2_2.png" class="pa mak" alt="">
                    <div class="txBut y" onclick="txFun()">
                        提现到区块钱包
                    </div>
                </div>
            </div>
        </div>
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
<script src="../js/jquery-2.2.2.min.js"></script>
<script>
    $(".txMask,.main").css({
        height:$(window).height(),
        width:$(window).width()
    });
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