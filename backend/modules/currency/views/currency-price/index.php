<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Currency;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CurrencyPriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '币种金额列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-price-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('添加币种金额', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    [
                        'attribute' => 'currency_id',
                        'value' => function($model){
                            $currencyData = ArrayHelper::map(Currency::find()->all(), 'id', 'symbol');
                            return $currencyData[$model->currency_id];
                        },
                        'filter' => ArrayHelper::map(Currency::find()->all(), 'id', 'symbol')
                    ],
                    'symbol',
                    'price',
                    'poundage',
                    'updated_at:datetime',
                    'updated_date',
                    [
                        'attribute' => 'is_exchange',
                        'value' => function($model){
                            return $model->is_exchange == 1 ? '兑换':'不能兑换';
                        },
                    ],

                    //'source',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
                </div>
    </div>

</div>
