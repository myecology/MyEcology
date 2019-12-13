<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\node\MallNodeLog */

$this->title = 'Create Mall Node Log';
$this->params['breadcrumbs'][] = ['label' => 'Mall Node Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-node-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
