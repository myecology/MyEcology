<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\activation\models\search\ActivationParamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '活动参数';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-param-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加活动参数', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'key',
            'value',
            [
                'attribute' => 'type',
                'value' => function($model){
                    $statusArray = \common\models\activation\ActivationParam::$typeArr;
                    return $statusArray[$model->type];
                },
                'filter' => \common\models\activation\ActivationParam::$typeArr
            ],
            'group',
            'remark',
            [
                'label' => '活动名称',
                'attribute' => 'name',
                'value' => 'activation.name',
            ],
            'created_at',
        ],
    ]); ?>
</div>
