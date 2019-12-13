<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ShopType */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shop Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-type-view">

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('删除', ['delete', 'id' => $model->id], [
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
            'title',
            'icon',
            'created_at',
            'updated_at',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
