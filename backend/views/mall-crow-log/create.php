<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\crow\MallCrowLog */

$this->title = 'Create Mall Crow Log';
$this->params['breadcrumbs'][] = ['label' => 'Mall Crow Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-crow-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
