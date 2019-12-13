<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\assets\models\GiftMoney */

$this->title = 'Create Gift Money';
$this->params['breadcrumbs'][] = ['label' => 'Gift Moneys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-money-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
