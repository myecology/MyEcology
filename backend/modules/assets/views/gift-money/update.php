<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\assets\models\GiftMoney */

$this->title = 'Update Gift Money: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Gift Moneys', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gift-money-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
