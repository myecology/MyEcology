<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\activation\ActivationParam */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Activation Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-param-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'key',
            'value',
            'created_at',
            'type',
            'group',
            'activation_id',
        ],
    ]) ?>

</div>
