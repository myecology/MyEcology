<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\Models\SettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '参数设置';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加参数', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
            'name',
            'key',
            // 'value',
            'group',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
