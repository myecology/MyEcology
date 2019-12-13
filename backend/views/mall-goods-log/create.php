<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\shop\MallGoodsLog */

$this->title = 'Create Mall Goods Log';
$this->params['breadcrumbs'][] = ['label' => 'Mall Goods Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mall-goods-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
