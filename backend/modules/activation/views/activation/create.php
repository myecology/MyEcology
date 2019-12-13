<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\activation\Activation */

$this->title = '创建活动';
$this->params['breadcrumbs'][] = ['label' => 'Activations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
