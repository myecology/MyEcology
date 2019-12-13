<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\assets\models\Invitation */

$this->title = 'Update Invitation: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Invitations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invitation-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
