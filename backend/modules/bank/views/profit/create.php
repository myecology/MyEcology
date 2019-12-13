<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\bank\Profit */

$this->title = 'Create Profit';
$this->params['breadcrumbs'][] = ['label' => 'Profits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
