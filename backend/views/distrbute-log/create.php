<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DistrbuteLog */

$this->title = 'Create Distrbute Log';
$this->params['breadcrumbs'][] = ['label' => 'Distrbute Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="distrbute-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
