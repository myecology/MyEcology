<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ReleaseDistrbuteLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '通证释放记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="release-distrbute-log-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],

            'id',
            // 'user_id',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            'amount',
            'symbol',
            'distrbute_id',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\ReleaseDistrbuteLog::$statusArr;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\ReleaseDistrbuteLog::$statusArr
            ],
            'created_at',
            'remark',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
