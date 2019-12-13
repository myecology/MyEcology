<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Crowdfunding */

$this->title = '创建众筹';
$this->params['breadcrumbs'][] = ['label' => '众筹列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crowdfunding-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
