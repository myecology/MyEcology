<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CurrencyPrice */

$this->title = '添加币种金额';
$this->params['breadcrumbs'][] = ['label' => '币种金额列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-price-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
