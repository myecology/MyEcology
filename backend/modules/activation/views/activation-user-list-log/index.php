<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\activation\models\search\ActivationUserListLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '增加用户等级日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-user-list-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            [
                'label' => '下级用户',
                'attribute' => 'pusername',
                'value' => 'puid.username',
            ],
            [
                'label' => '活动名称',
                'attribute' => 'name',
                'value' => 'activation.name',
            ],
            'number',
            'goods_name',
            'created_at',
        ],
    ]); ?>
</div>