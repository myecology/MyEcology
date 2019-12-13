<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\node\NodeWt */

$this->title = '修改节点: ' . $model->name;
// $this->params['breadcrumbs'][] = ['label' => 'Node Wts', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="node-wt-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
