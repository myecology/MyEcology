<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>共识计划</title>
    <meta name = "format-detection" content = "telephone=no">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/css/modules-bank/common.css">
    <link rel="stylesheet" href="/css/modules-bank/index.css">
</head>

<style>
.jp>input{
    box-sizing: border-box;
    border:1px solid #e6e6e6;
    height:60px;
    text-align: center;
    line-height:60px;
    font-weight: bold;
    width:33.3%;
    font-size:20px;
}
.jp input:nth-child(3n){
    width:33.4%;
}
.null{
    background-color:#e6e6e6;
}
input[type="button"], input[type="submit"], input[type="reset"] {
-webkit-appearance: none;
}
</style>

<body>
    <div class="main">
        <div class="boxTop">
            <div class="boxTitle pr">
                <div class="titleText ellipse"><?= $product->name ?></div>
                <a href="javascript:void(0)" class="more">
                    <img src="/images/modules-bank/more.png" alt="">
                </a>
            </div>
            <div class="boxHint pr cl">
                <div class="z hintText">低风险</div>
                <div class="z hintText"><?= sprintf("%.2f", $product->min_amount) ?>枚起投</div>
                <div class="z hintText">定期产品</div>
            </div>
        </div>
        <div class="box">
            <div class="ti">理财规则</div>
            <div class="boxH cl">
                <div class="z">
                    <p>锁仓日</p>
                    <p><?= date('Y/m/d') ?></p>
                </div>
                <div class="z">
                    <p>计息日</p>
                    <p><?= date('Y/m/d', time() + 86400) ?></p>
                </div>
                <div class="z">
                    <p>到期日</p>
                    <p><?= date('Y/m/d', time() + ($product->day + 1) * 86400) ?></p>
                </div>
            </div>
            <div class="rulesHint">购买后不可撤销，锁定周期<?= $product->day ?>天，到期前不可取出</div>
            <div class="de" onclick="lookDe()">
                产品详情<img src="/images/modules-bank/shou.png">
            </div>
            <div class="details none">
                <div class="cl">
                    <div class="z">产品名称</div>
                    <div class="y"><?= $product->name ?></div>
                </div>
                <div class="cl">
                    <div class="z">起始额度</div>
                    <div class="y"><?= sprintf("%.2f", $product->min_amount) .'枚'. $product->symbol ?></div>
                </div>
                <div class="cl">
                    <div class="z">额度上限</div>
                    <div class="y"><?= sprintf("%.2f", $product->user_amount) .'枚'. $product->symbol?></div>
                </div>
                <div class="cl">
                    <div class="z">存续期</div>
                    <div class="y"><?= date('Y.m.d', $product->statime) . '-' . date('Y.m.d', $product->endtime) ?></div>
                </div>
                <div class="cl">
                    <div class="z">锁定期限</div>
                    <div class="y"><?= $product->day ?>天</div>
                </div>
                <div class="cl">
                    <div class="z">到期收益率</div>
                    <div class="y"><?= sprintf("%.2f", $product->rate) ?>%</div>
                </div>
                <div class="cl">
                    <div class="z">收益分配方式</div>
                    <div class="y"><?= $product->income_description ?></div>
                </div>
                <div class="cl">
                    <div class="z">费用</div>
                    <div class="y">
                        无申购、管理费用
                    </div>
                </div>
                <div class="cl">
                    <div class="z">产品说明</div>
                    <div class="y"><?= $product->description ?></div>
                </div>
            </div>
            <div class="shop">
                <div class="shopBox cl">
                    <div class="z">理财数量</div>
                    <input type="text" class="y" id="inputAmount" data-symbol="<?= $product->symbol?>" pattern="[0-9]*" placeholder="请输入购买数量(<?= sprintf("%.2f", $product->min_amount) ?>起投)">
                </div>
            </div>
            <div class="shopInputBox cl pr">
                <img src="/images/modules-bank/st2_2.png" class="msk_1 pa none" alt="">
                <img src="/images/modules-bank/st2_2.png" class="msk_2 pa none" alt="">
                <img src="/images/modules-bank/st2_2.png" class="msk_3 pa none" alt="">
                <img src="/images/modules-bank/st2_2.png" class="msk_4 pa none" alt="">
                <div class="z pr" onclick="butClick(0)">
                    25%
                </div>
                <div class="z pr" onclick="butClick(1)">
                    50%
                </div>
                <div class="z pr" onclick="butClick(2)">
                    75%
                </div>
                <div class="z pr" onclick="butClick(3)">
                    100%
                </div>
            </div>
            <div class="numBox cl">
                <p>预计每日收益(<?= $product->earn_symbol?>):<span id="productProfit">0</span></p>
                <p>可用（<?= $product->symbol?>):<span id="amount"><?= sprintf("%.8f", $wallet['amount'] - $wallet['amount_lock']) ?></span></p>
            </div>
            <div class="shopBut" onclick="shopButFun()">
                立即理财
            </div>
            <div class="shopHint">注意：锁仓后到期才能解锁</div>
        </div>
    </div>
<div class="mask none">
    <div class="shopD pr">
        <div class="close" onclick="closeMask()">+</div>
        <p>请输入支付密码</p>
        <div class="num" id="startNum"></div>
        <p><?= $product->name ?></p>
        <div class="cl pass">
            <div class="z"></div>
            <div class="z"></div>
            <div class="z"></div>
            <div class="z"></div>
            <div class="z"></div>
            <div class="z"></div>
        </div>
        <!-- <div class="errorHint" id="error">密码不正确,请重新输入</div> -->
    </div>
    <div class="jp cl">
        <input type="button" class='z mm' onclick="paFun(1)" value="1">
        <input type="button" class='z mm' onclick="paFun(2)" value="2">
        <input type="button" class='z mm' onclick="paFun(3)" value="3">
        <input type="button" class='z mm' onclick="paFun(4)" value="4">
        <input type="button" class='z mm' onclick="paFun(5)" value="5">
        <input type="button" class='z mm' onclick="paFun(6)" value="6">
        <input type="button" class='z mm' onclick="paFun(7)" value="7">
        <input type="button" class='z mm' onclick="paFun(8)" value="8">
        <input type="button" class='z mm' onclick="paFun(9)" value="9">
        <input type="button" class='z mm null' >
        <input type="button" class='z mm' onclick="paFun(0)" value="0">
        <input type="button" class='z mm null' onclick="paFun(10)" value="X">
    </div>
</div>

<div class="txMask none">
    <div class="box3 paAuto cl">
        <P id="error">交易成功</P>
        <div class="closeBut" onclick="closeTx()">
            确定
        </div>
    </div>
</div>


</body>
<script src="/js/jquery-2.2.2.min.js"></script>
<script src="/css/layer_mobile/layer.js"></script>
<script>
    $(".txMask,.main,.mask").css({
        height:$(window).height(),
        width:$(window).width()
    });
    $(".titleText").css({
        width:$(window).width()-60-40
    });
    $(".jd").animate({
        width:($(".loadingNum span").eq(0).text()/$(".loadingNum span").eq(2).text())*100+'%'
    },500);
    $(".shopInputBox .z").css({
        marginLeft:($(window).width()-60)*0.12/3
    });
    $(".shopInputBox .z").eq(0).css({
        marginLeft:0
    });
    $(".details .y").css({
        width:$(window).width()-60-90
    });

    $('#inputAmount').bind('input propertychange', function(ev){
        var inputAmount = $(this).val();
        var productRate = <?=$product->rate?>;
        var currency_price = <?=$product->currency_price?>;
        var earn_currency_price = <?=$product->earn_currency_price?>;
        var getProfit = inputAmount * productRate * currency_price / 100 / earn_currency_price;
        $('#productProfit').html(getProfit.toFixed(2));
    })
    function butClick(index){
       $(".shopInputBox > div").removeClass('butClick');
       $(".shopInputBox > div").eq(index).addClass('butClick');
       $(".shopInputBox img").hide();
       $(".shopInputBox img").eq(index).show();

       //  设置投资数量
        var rate = [25,50,75,100];
        var amount = $('#amount').html();
        var inputAmount = amount * rate[index] / 100;
        $('#inputAmount').val(inputAmount);

        //  收益
        //var productRate = <?//= $product->rate ?>//;
        // var getProfit = inputAmount * productRate / 100;
        var productRate = <?=$product->rate?>;
        var currency_price = <?=$product->currency_price?>;
        var earn_currency_price = <?=$product->earn_currency_price?>;
        var getProfit = inputAmount * productRate * currency_price / 100 / earn_currency_price;
        $('#productProfit').html(getProfit.toFixed(2));
    }
    var isShow = false;
    function lookDe(){
        if(isShow){
            $(".details").hide();
            $(".de img").removeClass('rotateImg');
            isShow = false;
        }else{
            $(".details").show();
            $(".de img").addClass('rotateImg');
            isShow = true;
        }
    }
    function shopButFun(){
        var amount = $('#inputAmount').val();
        if(!amount){
            $('#error').html('请输入购买数量');
            $('.txMask').show();
            return false;
        }
        var symbol = $('#inputAmount').data('symbol');
        $('#startNum').html( amount + symbol );
        $(".mask").show();
    }
    var passLength = 0;
    var mm = '';
    function paFun(index){
        if (index==10){
            passLength--;
            if (passLength<0){
                passLength = 0;
            }
            $(".pass > div").eq(passLength).text('');
        }else{
            if(passLength<6){
                mm = mm + index;
                $(".pass > div").eq(passLength).text('*');
                passLength++;
                if (passLength==6){
                    passLength--;
                    yzFun();
                }
            }
        }
    }

    function yzFun(){
        var amount = $('#inputAmount').val();
        var password = mm;
        layer.open({
            shadeClose: false
            ,type: 2
            ,content: '加载中'
        });
        $.post('/bank/index/add-order', {"Order" : {"earn_currency_price" : "<?= $product->earn_currency_price ?>","currency_price" : "<?= $product->currency_price ?>","earn_symbol" : "<?= $product->earn_symbol ?>","symbol" : "<?= $product->symbol ?>", "product_id" : <?=$product->id?>, "amount" : amount, "day" : <?=$product->day?>, "password" : password}, "_csrf-frontend" : "<?=Yii::$app->request->csrfToken?>"}, function(obj){
            mm = '';
            passLength = 0;
            $(".pass > div").text('');
            $(".mask").hide();
            $(".errorHint").css({opacity:'1'});
            
            if(obj.status == 1){
                window.location.href = '/bank/index/index';
            }else{
                $('#error').html(obj.msg);
                $('.txMask').show();
                layer.closeAll();
            }
        }, 'json');
    }
    function closeMask(){
        $(".pass > div").text('');
        $(".mask").hide();
    }
    function closeTx(){
        $(".txMask").hide();
    }
</script>
</html>