<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\CurrencyParam */

$this->title = '添加参数';
$this->params['breadcrumbs'][] = ['label' => '参数列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-param-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
