<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MallGoodsActivityLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '节点收益';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-goods-activity-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'type',
                'value' => function($model){
                    $statusArray = \common\models\shop\MallGoodsActivityLog::$typeArr;
                    return $statusArray[$model->type];
                },
                'filter' => \common\models\shop\MallGoodsActivityLog::$typeArr
            ],
            // 'type',
            // 'mall_goods_id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'amount',
            'symbol',
            'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
