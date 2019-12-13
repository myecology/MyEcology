<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Transfer */

$this->title = 'Create Transfer';
$this->params['breadcrumbs'][] = ['label' => 'Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
