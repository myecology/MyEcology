<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\activation\models\search\ActivationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('创建活动', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            'as_name',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\activation\Activation::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\activation\Activation::$statusArr
            ],
            'created_at',
            'updated_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {param} {view}',
                'buttons' =>[
                    'param' => function($url, $model, $key){
                        return Html::a('配置参数', $url, [
                            'class' => 'btn btn-success btn-xs',
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
</div>
