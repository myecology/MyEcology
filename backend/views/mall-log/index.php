<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MallLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '购买记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-log-index">

    
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
            'goods_name',
            'number',
            'symbol',
            'amount',
            'remark',
            'order_sn',
            // 'activity',
            // 'activity_name',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\shop\MallLog::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\shop\MallLog::$statusArr
            ],
            'created_at',
            //'updated_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
