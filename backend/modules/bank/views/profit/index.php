<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bank\models\search\ProfitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收益日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username'
            ],
//            'product_id',
            'order_id',
            [
                'attribute' => 'type',
                'value' => function($model){
                    $statusArray = \common\models\bank\Profit::$typeArr;
                    return $statusArray[$model->type];
                },
                'filter' => \common\models\bank\Profit::$typeArr,
            ],
            'amount',
            'symbol',
            'created_at:datetime',
        ],
    ]); ?>
</div>
