<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserTreeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户关系网';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-tree-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            // ['class' => 'yii\grid\SerialColumn'],

                            'id',
                            'name',
                            // 'root',
                            // 'lft',
                            // 'rgt',
                            'lvl',
                            // 'name',
                            //'icon',
                            //'icon_type',
                            'node',
                            'userid',
                            //'uid',
                            //'active',
                            //'selected',
                            //'disabled',
                            //'readonly',
                            //'visible',
                            //'collapsed',
                            //'movable_u',
                            //'movable_d',
                            //'movable_l',
                            //'movable_r',
                            //'removable',
                            //'removable_all',
                            //'child_allowed',
                            'created_at:datetime',
                            // [
                            //     'class' => 'yii\grid\ActionColumn',
                            // ],
                        ],
                    ]); ?>
                </div>
    </div>

</div>
