<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\activation\ActivationUserList */

$this->title = 'Create Activation User List';
$this->params['breadcrumbs'][] = ['label' => 'Activation User Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-user-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
