<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\node\MallNodeLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Node Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-node-log-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            // 'user_id',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'alte_price',
            'alte_symbol',
            // 'alte_status',
            [
                'attribute' => 'alte_status',
                'value' => function($model){
                    return \common\models\node\MallNodeLog::$altestatusArr[$model->alte_status];
                },
            ],
            'alte_at',
            'super_price',
            'super_symbol',
            // 'super_status',
            [
                'attribute' => 'super_status',
                'value' => function($model){
                    return \common\models\node\MallNodeLog::$superstatusArr[$model->super_status];
                },
            ],
            'super_at',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
