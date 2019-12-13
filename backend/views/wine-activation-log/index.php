<?php

use yii\helpers\Html;
use common\models\shop\WineActivationLog;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\WineActivationLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '挖矿收益';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-activation-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
             'activation_id',
//            [
//                'attribute' => 'activation_id',
//                'value' => function($model){
//                    $statusArray = \common\models\shop\WineActivationLog::$typeArr;
//                    return $statusArray[$model->activation_id];
//                },
//                'filter' => \common\models\shop\WineActivationLog::$typeArr
//            ],
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
