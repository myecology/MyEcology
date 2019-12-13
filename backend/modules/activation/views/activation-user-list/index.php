<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\activation\models\search\ActivationUserListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动用户等级记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-user-list-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            [
                'label' => '活动名称',
                'attribute' => 'name',
                'value' => 'activation.name',
            ],
            'level',
            'created_at',
            'updated_at',
            [
                'attribute'=>'end_time',
                'value'=>function($model){
                    return   $model->end_time > 0 ? date('Y-m-d H:i:s',$model->end_time): '未设置';
                }
            ],
        ],
    ]); ?>
</div>
