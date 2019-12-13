<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\DateTimePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;



/* @var $this yii\web\View */
/* @var $searchModel backend\modules\member\models\search\WalletLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '通证分红日志列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallet-log-index table-responsive">

    <?php  // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">

            <form action="/distribute-ant/download-excel" method="get" >
                开始时间：<input type="date" name="start_time">
                结束时间：<input type="date" name="end_time">
                <input type="submit" value="下载excel">
            </form>
            <br>
            <form action="/distribute-ant/message" method="get" >
                名称:<input type="text" name="name" />
                发放币种：<select name="symbol">
                            <?php foreach($rewardlist as $val){ ?>
                                <option value="<?= $val ?>"><?= $val ?></option>
                            <?php } ?>
                        </select>
                发放数量：<input type="text" name="share_ant_number">
                持有币种：<select name="reward_symbol" onchange="totalnumber(this)">
                                <?php foreach($symbollist as $val){ ?>
                                    <option value="<?= $val ?>"><?= $val ?></option>
                                <?php } ?>
                            </select>

                <input type="submit" value="发放" >
                <br/>
                <span>持有<span class="danwei"><?=$symbollist[0] ?></span>人数：<span style="font-size:25px;color: red " class="people"><?= $count_antToken ;?></span> 人</span>
                <span style="margin-left: 50px">总计：<span style="font-size:25px;color: red " class="num"><?= $all_antToken ;?></span>  <span class="danwei"><?= $symbollist[0] ?></span></span>


            </form>
                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
            // 'wallet_id',
            'symbol',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'type',
            /*[
                'label' => 'AntToken',
                'attribute' => 'AntToken',
                'value' => 'wallet.amount',
            ],*/
            'amount',
            // 'balance',
            'created_at:datetime',
            'remark:ntext',
            //'business_sn',

                    // ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

<script type="text/javascript">
    function totalnumber(obj){
        $symbol = $(obj).val();
        var url = "<?= Url::toRoute('distribute-ant/ajax-total')?>";
        $.post(url,{symbol:$symbol},function(data){
            var dataD = JSON.parse(data);
            $(".danwei").text($symbol);
            $(".people").text(dataD.count);
            $(".num").text(dataD.total);
        });
    }
</script>

</div>
