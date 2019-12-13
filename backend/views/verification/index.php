<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Verification;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\VerificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '认证列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

//            'verification_sn',
            [
                'label' => '账号',
                'attribute' => 'username',
                'value' => 'user.username',
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
            //'name',
//            'reviewed_at:datetime',
            [
                'attribute' => 'image_main',
                'value' => function($model){
                    return Html::img($model->image_main, ['width' => 50, 'height'=> 50]);
                },
                'format' => 'raw',
            ],
//            [
//                'attribute' => 'image_1',
//                'value' => function($model){
//                    return Html::img($model->image_main, ['width' => 50, 'height'=> 50]);
//                },
//                'format' => 'raw',
//            ],
//            [
//                'attribute' => 'image_2',
//                'value' => function($model){
//                    return Html::a(Html::img($model->image_main, ['width' => 50, 'height'=> 50]));
//                },
//                'format' => 'raw',
//            ],
            'created_at:datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}  {success}  {refused}',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('查看', $url, [
                            'class' => 'btn btn-info btn-xs',
                        ]);
                    },
                    'success' => function($url, $model, $key){
                        if($model->status == \common\models\Verification::STATUS_SUBMITTED) {
                            return Html::a('通过', $url, [
                                'class' => 'btn btn-success btn-xs',
                                'data' => [
                                    'confirm' => '是否通过认证',
                                ]
                            ]);
                        }
                    },
                    'refused' => function($url, $model, $key){
                        if($model->status == \common\models\Verification::STATUS_SUBMITTED) {
                            return Html::a('拒绝', $url, [
                                'class' => 'btn btn-warning btn-xs',
                                'data' => [
                                    'confirm' => '是否拒绝认证',
                                ]
                            ]);
                        }
                    }
                ],
            ],
                ],
            ]); ?>
                </div>
    </div>

</div>
