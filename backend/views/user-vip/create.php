<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\UserVip */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '管委会列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-vip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
