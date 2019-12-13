<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ShopTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '行业类别';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-type-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

//                    'id',
            'title',
            'icon',
            'created_at:datetime',
            'updated_at:datetime',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
