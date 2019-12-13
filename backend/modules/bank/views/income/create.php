<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\bank\Income */

$this->title = '添加收益';
$this->params['breadcrumbs'][] = ['label' => '收益列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="income-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
