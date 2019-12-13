<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\bank\Income */

$this->title = '更新收益：' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '收益列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="income-update">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
