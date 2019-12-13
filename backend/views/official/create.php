<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Official */

$this->title = '添加系统通知';
$this->params['breadcrumbs'][] = ['label' => 'Officials', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="official-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
