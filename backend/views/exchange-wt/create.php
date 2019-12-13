<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ExchangeWt */

$this->title = 'Create Exchange Wt';
$this->params['breadcrumbs'][] = ['label' => 'Exchange Wts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exchange-wt-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
