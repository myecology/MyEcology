<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ShopType */

$this->title = '更新行业类别 - '.$model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shop Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shop-type-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
