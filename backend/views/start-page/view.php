<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\StartPage */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Start Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="start-page-view">

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
            'img',
            'name',
            'type',
            'sort',
            'status',
            'time:datetime',
            'created_at',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
