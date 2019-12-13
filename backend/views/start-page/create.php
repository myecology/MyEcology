<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\StartPage */

$this->title = '添加启动页图片';
$this->params['breadcrumbs'][] = ['label' => '启动页列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="start-page-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
