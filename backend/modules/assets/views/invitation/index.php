<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\assets\models\search\InvitationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '邀请记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitation-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    // 'registerer_id',
                    // 'inviter_id',
                    [
                        'label' => '注册人',
                        'attribute' => 'registerUsername',
                        'value' => 'registerUser.username',
                    ],
                    [
                        'label' => '邀请人',
                        'attribute' => 'inviterUsername',
                        'value' => 'inviterUser.username',
                    ],
                    'level',
                    'created_at:datetime',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}'
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
