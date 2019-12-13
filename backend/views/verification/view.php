<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Verification */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Verifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-view">

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
//                    'id',
//            'verification_sn',
//            'user_id',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            'name',
            'identity_number',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\Verification::$lib_status;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\Verification::$lib_status
            ],
            [
                'attribute' => 'image_main',
                'value' => function($model){
                    return Html::img($model->image_main, ['width' => 50, 'height'=> 50]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'image_1',
                'value' => function($model){
                    return Html::img($model->image_1, ['width' => 50, 'height'=> 50]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'image_2',
                'value' => function($model){
                    return Html::img($model->image_2, ['width' => 50, 'height'=> 50]);
                },
                'format' => 'raw',
            ],
            'created_at:datetime',
            'reviewed_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
