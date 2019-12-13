<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\WineLevelLog */

$this->title = 'Create Wine Level Log';
$this->params['breadcrumbs'][] = ['label' => 'Wine Level Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wine-level-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
