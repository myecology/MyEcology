<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\Currency;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CurrencyParamSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '参数列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-param-index table-responsive">

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
                    [
                        'attribute' => 'id',
                        'headerOptions' => [
                            'width' => 50,
                        ],
                    ],
                    // 'currency_id',
                    [
                        'attribute' => 'currency_id',
                        'value' => function ($model) {
                            $currencyData = ArrayHelper::map(Currency::find()->all(), 'id', 'symbol');
                            return $currencyData[$model->currency_id];
                        },
                        'filter' => ArrayHelper::map(Currency::find()->all(), 'id', 'symbol')
                    ],
                    'symbol',
                    'key',
                    [
                        'attribute' => 'value',
                        'contentOptions' => [
                            'class' => 'contentTd',
                        ],
                        'value' => function ($model) {
                            return strlen($model->value) > 128 ? mb_substr($model->value, 0, 128, 'utf-8') . '...' : $model->value;
                        },
                    ],
                    'updated_at:datetime',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => [
                            'width' => 80,
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
