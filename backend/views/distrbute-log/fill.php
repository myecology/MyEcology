<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\FillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '补发列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="button" style="float:right;"><button onclick="clickon()">补发</button></div>
<div class="fill-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'symbol',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\Fill::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\Fill::$statusArr
            ],
            'release_symbol',
            'amount',
            //'is_del',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <!-- <input id="hidden" type="hidden" value= /> -->
</div>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script type="text/javascript">
    function GetQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
    function clickon(){
        var url = "<?= Url::toRoute('distribute-ant/fill-one'); ?>";
        var id = GetQueryString("id");
        $.post(url,{id:id},function(obj){
            var data = JSON.parse(obj);
            alert(data.msg);
            if(data.status == 200){
                localStorage.removeItem("rs");
                localStorage.removeItem("san");
                localStorage.removeItem("sym"); 
                localStorage.removeItem("allData");
                localStorage.removeItem("times");  
                window.location.href = "";
            }
            // console.log(data);
        });
    }
</script>
