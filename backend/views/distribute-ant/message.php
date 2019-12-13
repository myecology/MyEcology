<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\DateTimePicker;
use yii\widgets\ActiveForm;
use yii\helpers\Url;



/* @var $this yii\web\View */
/* @var $searchModel backend\modules\member\models\search\WalletLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '通证分红信息确认';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wallet-log-index table-responsive">
    <div class="box box-success">
        <div class="box-body">
            <h3>即将发送 <span style="color: red"><?php echo $share_ant_number?></span> 个<?=$symbol ?>作为通证分红</h3>
           <span style="float: right">
               <form action="/distribute-ant/share-bonus" method="get">
                   <input type="text" name="share_ant_number" value="<?php echo $share_ant_number?>" hidden>
                   <input type="text" name="symbol" value="<?php echo $symbol?>" hidden>
                   <input type="text" name="reward_symbol" value="<?php echo $reward_symbol?>" hidden>
                   <input type="text" name="name" value="<?php echo $name?>" id="onlyname" hidden>
                 <input type="submit" onclick="this.disabled=true; this.value='发放中....'; this.form.submit();" value="全部发放" />
               </form>
               <input type="submit" value="一键添加" onclick="oneButAdd()" />
           </span>

        </div>
        <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
          // ['class' => 'yii\grid\SerialColumn'],
          ['class'=>\yii\grid\CheckboxColumn::className(),
          'checkboxOptions' => function ($model, $key, $index, $column) {
               return ['value'=>$model->user_id,'class'=>'checkbox','onchange'=>'aa(this)'];
          }],
            // 'id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'symbol',
            'amount',
          ],
        ]); ?>
    </div>

</div>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="../../../mobile/layer.js"></script>
<script type="javascript/css" src="../../../mobile/need/layer.css"></script>
<link href="../../../mobile/need/layer.css" rel="stylesheet">
<script> 

  function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
  }
    var allArray = [];
    var maxChekbox = $(".checkbox").length;
    var nowChekbox = 0;
    allArray = localStorage.getItem("allData");
    if(allArray){
        allArray = allArray.split(",");
        for(var i = 0;i<allArray.length;i++){
            for(var e = 0;e<$('.checkbox').length;e++){
                if(parseInt($(".checkbox").eq(e).val()) == parseInt(allArray[i])){
                    $(".checkbox").eq(e).prop("checked", true);
                    nowChekbox++;
                }
            }
        }
     }else{
        allArray = [];
    }

    if(nowChekbox == maxChekbox){
        $(".select-on-check-all").prop("checked", true);
    }


    function aa(obj){
        console.log($(obj).is(':checked'));
        if($(obj).is(':checked')){
            console.log(allArray);
            allArray.push($(obj).val());
        }else{
            allArray = delArrVal(allArray, $(obj).val());
        }
        localStorage.setItem("allData",allArray);
    }


    function ca(obj){
      console.log("!@!")
       if($(obj).is(':checked')){
           for(var i = 0;i<$(".checkbox").length;i++){ 
                   $('.checkbox').eq(i).prop("checked", true);
                   allArray.push($('.checkbox').eq(i).val());
                   localStorage.setItem("allData",allArray);
            }
       }else{
           for(var i = 0;i<$(".checkbox").length;i++){
                $(".checkbox").prop("checked", false);
               localStorage.removeItem("allData");
               allArray = delArrVal(allArray,$(".checkbox").eq(i).val());
               localStorage.setItem("allData",allArray);
           }
       }
    }


    function newArr(array){
        //一个新的数组
        var arrs = [];
        //遍历当前数组
        for(var i = 0; i < array.length; i++){
            //如果临时数组里没有当前数组的当前值，则把当前值push到新数组里面
            if (arrs.indexOf(array[i]) == -1){
                arrs.push(array[i])
            };
        }
        return arrs;
    }



    function delArrVal(arr,val){
        for(let i=0;i<arr.length;i++){
            if(arr[i]==val){
                arr.splice(i,1);
                i--;
            }
        }
        return arr;
    }


    function oneButAdd(){
        if(allArray){
            allArray = newArr(allArray);
        }
        for(var i = 0;i<allArray.length;i++){
            for(var e = 0;e<$('.checkbox').length;e++){
                if(parseInt($(".checkbox").eq(e).val()) == parseInt(allArray[i])){
                    $(".checkbox").eq(e).prop("checked", false);
                }
            }
        }
        var dataD = allArray;
        var timesData = localStorage.getItem("times");
        var data_1 = localStorage.getItem("rs");
        var data_2 = localStorage.getItem("san");
        var data_3 = localStorage.getItem("sym");

        var getRs = GetQueryString('reward_symbol');
        var getSan = GetQueryString('share_ant_number');
        var getSymbol = GetQueryString('symbol');
        var getname = $("#onlyname").val();
        if(data_1 != getRs ||data_2 != getSan ||data_3 != getSymbol){
          timesData = '';
        }else{
          layer.open({
            title: '提示信息'
            ,content: '还有未发放的通证分红，请前往通证列表发放!!!'
          }); 
          return false;
        }
        $.ajax({
          type: "post",
          url:"<?= Url::toRoute('distribute-ant/ajax-add')?>",
          data:{
            user:dataD,
            name:getname,
            symbol:getSymbol,
            amount:getSan,
            release_symbol:getRs,
            ids:timesData
          },
          success: function(data){
            var objs = JSON.parse(data);
            alert(objs.msg);
            localStorage.setItem("rs",getRs);
            localStorage.setItem("san",getSan);
            localStorage.setItem("sym",getSymbol); 
            localStorage.setItem("times", objs.data);
            localStorage.removeItem("allData");
            if(objs.status == 200){
              var href = "<?= Url::toRoute('/distrbute-log/view')?>";
              window.location.href = href+"?id="+objs.data;
            }
            return false;
          },
          error:function(res){
          }
        });
    }
     

      
</script>






