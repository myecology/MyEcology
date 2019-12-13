<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\VersionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'APP版本';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="version-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('更新IOS版本', ['create', 'type' => 2], ['class' => 'btn btn-success']) ?>
                <?= Html::a('更新Android版本', ['create', 'type' => 1], ['class' => 'btn btn-success']) ?>
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
                    [
                        'attribute' => 'update',
                        'value' => function($model){
                            $updateArray = $model::updateArray();
                            return $updateArray[$model->update];
                        },
                        'filter' => $searchModel::updateArray(),
                    ],
            'num',
            // 'update',
            'version',
            'size',
            'url:url',
            'content:ntext',
            'created_at:datetime',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
