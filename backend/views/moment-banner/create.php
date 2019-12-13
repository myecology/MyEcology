<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\MomentBanner */

$this->title = '添加Banner';
$this->params['breadcrumbs'][] = ['label' => 'Banner列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moment-banner-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
