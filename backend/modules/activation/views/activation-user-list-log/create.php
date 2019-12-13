<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\activation\ActivationUserListLog */

$this->title = 'Create Activation User List Log';
$this->params['breadcrumbs'][] = ['label' => 'Activation User List Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-user-list-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
