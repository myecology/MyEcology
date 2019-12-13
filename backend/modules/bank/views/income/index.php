<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\bank\search\IncomeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收益列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="income-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加收益', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            // 'id',
                            [
                                'attribute' => 'type',
                                'value' => function($model){
                                    $typeArray = $model::typeArray();
                                    return $typeArray[$model->type];
                                },
                                'filter' => $searchModel::typeArray(),
                            ],
                            'name',
                            'day',
                            // 'num',
                            'created_at:datetime',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
    </div>

</div>
