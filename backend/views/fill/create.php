<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Fill */

$this->title = 'Create Fill';
$this->params['breadcrumbs'][] = ['label' => 'Fills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fill-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
