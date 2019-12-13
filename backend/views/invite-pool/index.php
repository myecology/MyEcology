<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\InvitePoolSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '奖池列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-pool-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加奖池', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    // 'currency_id',
                    'name',
                    // 'type',
                    // 'symbol',
                    // 'amount',
                    // 'amount_left',
                    //'expired_at',
                    [
                        'attribute' => 'amount',
                        'value' => function($model){
                            $html = '总数：' . sprintf("%.2f", $model->amount) . '<br />';
                            $html .= '剩余：' . sprintf("%.2f", $model->amount_left);
                            return $html;
                        },
                        'format' => 'raw'
                    ],
                    // 'prize',
                    // 'prize_registerer',
                    // 'prize_inviter',
                    // 'prize_grand_inviter',
                    [
                        'attribute' => 'prize',
                        'value' => function($model){
                            $html = '注册奖励：' . $model->prize_registerer . '<br />';
                            $html .= '邀请奖励：' . $model->prize_inviter . '<br />';
                            $html .= '二级奖励：' . $model->prize_grand_inviter . '<br />';
                            $html .= '三级奖励：' . $model->prize_grand_grand_inviter;
                            return $html;
                        },
                        'format' => 'raw',
                    ],
                    'created_at:datetime',
                    // 'status',
                    [
                        'attribute' => 'status',
                        'value' => function($model){
                            $statusArray = $model::statusArray();
                            return $statusArray[$model->status];
                        },
                        'filter' => $searchModel::statusArray()
                    ],

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
