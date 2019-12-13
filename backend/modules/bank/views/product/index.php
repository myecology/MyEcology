<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\bank\Income;

/* @var $this yii\web\View */
/* @var $searchModel common\models\bank\search\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '产品列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加产品', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    'name',
                    // 'symbol',
                    [
                        'attribute' => 'income_id',
                        'value' => function($model){
                            $incomeArray = ArrayHelper::map(Income::find()->all(), 'id', 'name');
                            return $incomeArray[$model->income_id];
                        },
                        'filter' => ArrayHelper::map(Income::find()->all(), 'id', 'name')
                    ],
                    [
                        'label' => '属性',
                        'attribute' => 'max_amount',
                        'value' => function($model){
                            $html = '总量：' . sprintf("%.2f",$model->max_amount) . '<br />';
                            $html .= '数量：' . sprintf("%.2f",$model->amount) . '<br />';
                            $html .= '个人：' . sprintf("%.2f",$model->rate) . '<br />';
                            $html .= '节点：' . sprintf("%.2f",$model->super_rate) . '<br />';
                            $html .= '最小：' . sprintf("%.2f",$model->min_amount) . '<br />';
                            $html .= '最大：' . sprintf("%.2f",$model->user_amount) . '<br />';
                            return $html;
                        },
                        'format' => 'raw',
                    ],
                    // 'amount',
                    // 'rate',
                    // 'min_amount',
                    // 'max_amount',
                    //'income_id',
                    // 'income_description',
                    //'type',
                    //'fee',
                    //'fee_explain',
                    'day',
                    //'description',
                    'statime:datetime',
                    'endtime:datetime',
                    [
                        'attribute' => 'status',
                        'value' => function($model){
                            $statusArray = $model::statusArray();
                            return $statusArray[$model->status];
                        },
                        'filter' => $searchModel::statusArray(),
                    ],
                    //'created_at',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
