<?php

use common\models\bank\Income;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TokenAssetsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '释放信息';
$this->params['breadcrumbs'][] = ['label' => '代币设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="token-asstes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '人员类型',
                'attribute' => 'personnel_type',
                'value' => 'tokenAssets.personnel_type',
            ],
            [
                'label' => '资产类型',
                'attribute' => 'token_type',
                'value' => 'tokenType.name',
            ],
            [
                'label' => '备注',
                'attribute' => 'remark',
                'value' => 'tokenAssets.remark',
            ],
            [
                'label' => '用户手机号',
                'attribute' => 'phone',
                'value' => 'user.username',
            ],
            [
                'label' => '用户昵称',
                'attribute' => 'nickname',
                'value' => 'user.nickname',
            ],
            [
                'label' => '已释放数量',
                'attribute' => 'available_balance',
                'value' => 'available_balance',
            ],
            [
                'label' => '待释放数量',
                'attribute' => 'locked_balance',
                'value' => 'locked_balance',
            ],
            [
                'label' => '每次释放数量',
                'attribute' => 'every_time_number',
                'value' => 'every_time_number',
            ],
            [
                'label' => '已发放数量',
                'attribute' => 'count',
                'value' => 'count',
            ],
            [
                'label' => '状态',
                'attribute' => 'status',
                'value'=>function($model){
                    return    \common\models\RcUserToken::$statusArr[$model->status];
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete-user-assets}',
                'buttons' => [
                    'delete-user-assets' => function($url, $model, $key){
                        return Html::a('删除信息', $url, [
                            'title' => '删除信息',
                            'class' => 'btn btn-success btn-xs',
                            'data' => [
                                'confirm' => '确定删除该数据?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
