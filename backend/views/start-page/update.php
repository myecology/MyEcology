<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\StartPage */

$this->title = '启动页图片修改 > ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '启动页图片修改', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="start-page-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
