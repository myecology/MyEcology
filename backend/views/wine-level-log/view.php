<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\shop\WineLevelLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '邀请关系', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-level-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            [
                'label' => '邀请用户',
                'value' => function($model){
                    return $model->inviterUser->username;
                }
            ],
            'created_at:datetime',
            'number',
            'goods_name',
        ],
    ]) ?>

</div>
