<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CurrencyParam */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '参数列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-param-view">

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
            'currency_id',
            'symbol',
            'key',
            'value:ntext',
            'updated_at',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
