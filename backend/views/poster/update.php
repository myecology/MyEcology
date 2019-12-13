<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Poster */

$this->title = '更新海报 > ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '海报列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="poster-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
