<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\WineActivationLog */

$this->title = 'Create Wine Activation Log';
$this->params['breadcrumbs'][] = ['label' => 'Wine Activation Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-activation-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
