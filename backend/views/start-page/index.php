<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\StartPageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '启动页列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="start-page-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加启动页图片', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    //'name',
                    [
                        'attribute' => 'img',
                        'value' => function($model){
                            return Html::img($model->img, ['height' => 100]);
                        },
                        'format' => 'raw',
                    ],
                    /*
                    [
                        'attribute' => 'type',
                        'value' => function ($model) {
                            $statusArray = $model::typeArray();
                            return $statusArray[$model->status];
                        },
                        'filter' => $searchModel::typeArray()
                    ],
                    */
                    //'sort',
                    'name',
                    'time',
                    'created_at:datetime',
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
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
