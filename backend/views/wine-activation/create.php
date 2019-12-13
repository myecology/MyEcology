<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\WineActivation */

$this->title = 'Create Wine Activation';
$this->params['breadcrumbs'][] = ['label' => 'Wine Activations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-activation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
