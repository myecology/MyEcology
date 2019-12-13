<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\MallLog */

$this->title = 'Create Mall Log';
$this->params['breadcrumbs'][] = ['label' => 'Mall Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
