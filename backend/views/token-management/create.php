<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\RcTokenType */

$this->title = '创建代币类型';
$this->params['breadcrumbs'][] = ['label' => '代币类型', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rc-token-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
