<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\WineLevel */

$this->title = 'Create Wine Level';
$this->params['breadcrumbs'][] = ['label' => 'Wine Levels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-level-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
