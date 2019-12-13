<?php

use common\models\bank\Income;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TokenAssetsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '代币设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="token-asstes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('创建', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'token_type_id',
                'value'=>function($model){
                    $tokenArray = ArrayHelper::map(\common\models\RcTokenType::find()->all(), 'id', 'name');
                    return $tokenArray[$model->token_type_id];
                },
                'filter' => ArrayHelper::map(\common\models\RcTokenType::find()->all(), 'id', 'name')
            ],
            'personnel_type',
            'currency_total',
            'start_time:datetime',
            'end_time:datetime',
            'release_cycle',
            'remark',
            [
                'attribute' => 'type',
                'value' => function($model){
                    $typeArray = $model::typeArray();
                    return $typeArray[$model->type];
                },
                'filter' => $searchModel::typeArray(),
            ],

//            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{delete}{add-user}{release-information}',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('详细信息', $url, ['title' => '详细信息', 'class' => 'btn btn-success btn-xs']);
                    },
                    'delete' => function($url, $model, $key){
                        return Html::a('删除信息', $url, [
                             'title' => '删除信息',
                            'class' => 'btn btn-success btn-xs',
                            'data' => [
                                'confirm' => '确定删除该数据?',
                                'method' => 'post',
                                ],
                            ]);
                    },
                    'add-user' => function($url, $model, $key){
                        return Html::a('指定用户', $url, ['title' => '指定用户', 'class' => 'btn btn-success btn-xs']);
                    },
                    'release-information' => function($url, $model, $key){
                        return Html::a('查看释放信息', $url, ['title' => '查看释放信息', 'class' => 'btn btn-success btn-xs']);
                    }
                ],
            ],
        ],
    ]); ?>
</div>
