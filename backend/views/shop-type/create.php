<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ShopType */

$this->title = '添加行业类别';
$this->params['breadcrumbs'][] = ['label' => 'Shop Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-type-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
