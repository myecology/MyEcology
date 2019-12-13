<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\activation\ActivationParam */

$this->title = '添加活动参数';
$this->params['breadcrumbs'][] = ['label' => '活动参数', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-param-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
