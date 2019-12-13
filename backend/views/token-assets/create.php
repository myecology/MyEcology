<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\TokenAssets */

$this->title = '创建';
$this->params['breadcrumbs'][] = ['label' => '代币设置', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="token-asstes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
