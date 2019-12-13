<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\FillSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '未补发列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- <div class="button" style="float:right;"><button onclick="clickon()">补发</button></div> -->
<div class="fill-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            // 'id',
            [
                'label' => '通证名称',
                'attribute' => 'name',
                'value' => 'distrbute.name',
            ],
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'symbol',
            'status',
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
    /*function clickon(){
        var url = "<?= Url::toRoute('distribute-ant/fill-user')?>";
        $.post(url,function(data){
            console.log(data);
        });
    }*/
</script>
