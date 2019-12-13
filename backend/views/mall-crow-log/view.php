<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\crow\MallCrowLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mall Crow Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-crow-log-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            // 'crow_id',
            // 'user_id',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'crow_name',
            'number',
            'pay_symbol',
            'symbol',
            'pay_number',
            // 'status',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return \common\models\crow\MallCrowLog::$statusArr[$model->status];
                },
            ],
            // 'type',
            [
                'attribute' => 'type',
                'value' => function($model){
                    return \common\models\crow\MallCrowLog::$typeArr[$model->type];
                },
            ],
            'release_at',
            'release_cycle',
            'release_times',
            'each_release',
            'created_at',
            'release_end_at',
            'mall_crow_id',
            // 'is_release',
            [
                'attribute' => 'is_release',
                'value' => function($model){
                    return \common\models\crow\MallCrowLog::$releaseArr[$model->is_release];
                },
            ],
            'time',
            // 'flag',
        ],
    ]) ?>

</div>
