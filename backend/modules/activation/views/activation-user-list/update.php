<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\activation\ActivationUserList */

$this->title = 'Update Activation User List: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Activation User Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activation-user-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
