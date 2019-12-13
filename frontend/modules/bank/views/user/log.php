<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>交易记录</title>
    <meta name = "format-detection" content = "telephone=no">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <link rel="stylesheet" href="/css/modules-bank/common.css">
    <link rel="stylesheet" href="/css/modules-bank/his.css">
</head>
<body>
    <div class="main">
        <div class="titile cl pr">
            <img src="/images/modules-bank/st2_2.png" class="pa mak_1" alt="">
            <img src="/images/modules-bank/st2_2.png" class="pa mak_2 none" alt="">
            <img src="/images/modules-bank/st2_2.png" class="pa mak_3 none" alt="">
            <div class="z titileClick" onclick="showAll(0)">全部</div>
            <div class="z" onclick="showAll(1)">锁仓</div>
            <div class="z" onclick="showAll(2)">解锁</div>
        </div>
        <div class="allMes">

            <?php foreach($data as $val){

                switch ($val['type']) {
                    case \common\models\bank\Log::TYPE_LOCK_PRODUCT:
                        $wClass = 'status_1';
                        $labelName = '令牌锁仓';
                        $wColor = 'red';
                        $amount = '-' . $val->money;
                        $dateTime = '<div class="stateColor">锁仓时间</div><div>' . date('Y/m/d H:i', $val->created_at) . '</div>';
                        $symbolName = '<div>锁仓数量('.$val->order->symbol.')</div>';

                        break;
                    case \common\models\bank\Log::TYPE_UN_LOCK_PRODUCT:
                        $wClass = 'status_2';
                        $labelName = '令牌解锁';
                        $wColor = 'green';
                        $amount = '+' . $val->money;
                        $dateTime = '<div class="stateColor">解锁时间</div><div>' . date('Y/m/d H:i', $val->created_at) . '</div>';
                        $symbolName = '<div>解锁数量('.$val->order->symbol.')</div>';

                        break;
                    case \common\models\bank\Log::TYPE_PROFIT_PRODUCT:
                        $wClass = 'status_1';
                        $labelName = '令牌锁仓';
                        $wColor = 'red';
                        $amount = '-' . $val->money;
                        $dateTime = '<div class="stateColor">锁仓时间</div><div>' . date('Y/m/d H:i', $val->created_at) . '</div>';
                        $symbolName = '<div>锁仓数量('.$val->order->symbol.')</div>';

                        break;
                    case \common\models\bank\Log::TYPE_BACK_PRODUCT:
                        $wClass = 'status_2';
                        $labelName = '令牌退回';
                        $wColor = 'green';
                        $amount = '+' . $val->money;
                        $dateTime = '<div class="stateColor">退回时间</div><div>' . date('Y/m/d H:i', $val->created_at) . '</div>';
                        $symbolName = '<div>退回数量('.$val->order->symbol.')</div>';
                        break;
                    default:

                        break;
                }  
            ?>

            <div class="box <?= $wClass ?>">
                <div class="boxTitle cl">
                    <div class="z nameT stateColor"><?= $labelName ?></div>
                    <div class="y name"><?= $val->title ?></div>
                </div>
                <div class="boxMes cl">
                    <div class="z time"><?= $dateTime ?></div>
                    <div class="y">
                        <div class="stateColor"><?= sprintf("%.8f", $amount) ?></div><?= $symbolName ?></div>
                </div>
            </div>
            <?php } ?>
            
        </div>
    </div>
</body>
<script src="/js/jquery-2.2.2.min.js"></script>
<script>
    function showAll(state){
        $(".titile .z").removeClass('titileClick');
        $(".titile .z").eq(state).addClass('titileClick');
        $(".titile img").hide();
        $(".titile img").eq(state).show();
        switch (state){
            case 0:
                $(".box").show();
                break;
            case 1:
                $(".status_1").show();
                $(".status_2").hide();
                break;
            case 2:
                $(".status_1").hide();
                $(".status_2").show();
                break;
        }
    }
</script>
</html>