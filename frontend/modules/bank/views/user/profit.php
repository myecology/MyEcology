<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>收益明细</title>
    <meta name = "format-detection" content = "telephone=no">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/css/modules-bank/common.css">
    <link rel="stylesheet" href="/css/modules-bank/mingxi.css">
</head>
<body>
    <div class="main">
        <div class="mainTop pr">
            <div class="moneyCount paAuto">
                <p><?= $profitTotal ? sprintf("%.2f", $profitTotal) : 0 ?></p>
                累计收益(￥)
            </div>
        </div>
        <div class="boxAll">

            <?php foreach($data as $order){ 

                //  如果是本金+收益类型
                $income = $order->product->income;
                $avg = 0;
                $baseAmount = 0;
                /*
                if($income->type == \common\models\bank\Income::TYPE_FUNDS_FEE && ($income->day * $order->day) > 0){
                    $avg = $order->amount / $income->day * $order->day;
                    $baseAmount = $order->amount;
                }
                */
//                $maxAmount = (($order->amount * $order->rate * $order->currency_price / 100) / $order->earn_currency_price / $income->day * $order->day) + $baseAmount;
                $minAmount = 0;
                foreach($order->profit as $val){
                    if ($val->type == 0) {
                        $minAmount = $val->amount + $minAmount + $avg;
                    }
                }
            ?>

            <?php if($minAmount > 0){  ?>

            <div class="box">
                <div class="title cl <?= $order->status == \common\models\bank\Order::STATUS_END ? '' : 'pr'; ?>">
                    <div class="titleText z"><?= $order->product->name ?></div>
                    <?php if($order->status == \common\models\bank\Order::STATUS_END){
                        echo '<div class="titleState y titleStateOver">已结束</div>';
                    }else{
                        echo '<div class="titleState y titleStateIng pr">阶段释放</div>';
                    }
                    ?>
                    
                </div>
                <div class="boxMes cl">
                    <div class="mes z">
                        <div class="stateColor">理财金额（<?= $order->symbol ?>）</div>
                       <!-- <div> 锁仓总收益（<?/*= $order->earn_symbol */?>）</div>-->
                    </div>
                    <div class="mes y">
                        <div  class="stateColor"><?= $order->amount ?></div>
                        <!--<div> 理财币种（<?/*= $order->symbol */?>）</div>-->
                    </div>
                </div>
                <div class="hint">
                    理财到期时间：<?= date('Y/m/d H:i', $order->created_at + ($order->day + 1 + ($order->product->income->day * $order->product->income->num)) * 86400) ?>
                </div>
            </div>

            <?php }} ?>

        </div>
    </div>
</body>
<script src="/js/jquery-2.2.2.min.js"></script>
<script>

</script>
</html>