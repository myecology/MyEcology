<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TokenAssets */

$this->title = '修改';
$this->params['breadcrumbs'][] = ['label' => '代币设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="token-asstes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
