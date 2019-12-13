<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserTree */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User Trees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-tree-view">

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
            'root',
            'lft',
            'rgt',
            'lvl',
            'name',
            'icon',
            'icon_type',
            'node',
            'userid',
            'uid',
            'active',
            'selected',
            'disabled',
            'readonly',
            'visible',
            'collapsed',
            'movable_u',
            'movable_d',
            'movable_l',
            'movable_r',
            'removable',
            'removable_all',
            'child_allowed',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
