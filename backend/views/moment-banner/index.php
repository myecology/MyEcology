<?php

use Yii;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MomentBannerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '朋友圈Banner列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moment-banner-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加Banner', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    'title',
                    // 'url',
                    [
                        'attribute' => 'url',
                        'value' => function($model){
                            return Html::img($model->url, ['height' => 100]);
                        },
                        'format' => 'raw',
                    ],
                    'link:url',
                    'sort',
                    'created_at:datetime',
                    [
                        'attribute' => 'status',
                        'value' => function($model){
                            $statusArray = $model::statusArray();
                            return $statusArray[$model->status];
                        },
                        'filter' => $searchModel::statusArray()
                    ],

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
