<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\InvitePool */

$this->title = '更新' . $model->symbol;
$this->params['breadcrumbs'][] = ['label' => '奖池列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="invite-pool-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
