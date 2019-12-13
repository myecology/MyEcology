<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\bank\Income */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '收益列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="income-view">

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
            'name',
            'day',
            'num',
            'created_at',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
