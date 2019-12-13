<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\ExchangeWt;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ExchangeWtSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'WT1918兑换列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exchange-wt-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'e_symbol',
            'amount',
            'wt_number',
            'create_time',
            'symbol_price',
            'fee',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
</div>
