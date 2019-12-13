<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserTree */

$this->title = 'Update User Tree: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'User Trees', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-tree-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
