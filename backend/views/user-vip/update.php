<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\shop\UserVip */

$this->title = '修改管委会信息: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '管委会列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-vip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
