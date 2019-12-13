<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\node\NodeWt */

$this->title = '修改节点';
$this->params['breadcrumbs'][] = ['label' => 'Node Wts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="node-wt-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
