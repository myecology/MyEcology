<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\InvitePool */

$this->title = '添加奖池';
$this->params['breadcrumbs'][] = ['label' => '奖池列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-pool-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
