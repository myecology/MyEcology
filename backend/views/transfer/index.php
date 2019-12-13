<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '转出记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-index">


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            // 'sender_id',
            [
                'label' => '转出用户',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            // 'receiver_id',
            [
                'label' => '接受用户',
                'attribute' => 'rusername',
                'value' => 'receiver.username',
            ],
            'symbol',
            // 'currency_id',
            'amount',
            'created_at:datetime',
            'taken_at:datetime',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \backend\models\Transfer::$lib_status;
                    return $statusArray[$model->status];
                },
                'filter' => \backend\models\Transfer::$lib_status
            ],
            // 'description',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
