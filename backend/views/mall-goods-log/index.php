<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MallGoodsLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '购买记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-goods-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'goods_name',
            'order_sn',
            'number',
            // 'order_goods_id',
            'activity_name',
            //'activity',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\shop\MallGoodsLog::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\shop\MallGoodsLog::$statusArr
            ],
            // 'status',
            'created_at',
            'updated_at',
            'amount',
            'symbol',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
