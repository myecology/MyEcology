<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\MomentBanner */

$this->title = '更新Banner: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Banner 列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="moment-banner-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
