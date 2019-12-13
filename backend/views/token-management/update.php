<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RcTokenType */

$this->title = '修改代币类型';
$this->params['breadcrumbs'][] = ['label' => '代币类型', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="rc-token-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
