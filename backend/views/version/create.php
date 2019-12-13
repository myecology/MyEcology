<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Version */

$this->title = '更新版本';
$this->params['breadcrumbs'][] = ['label' => 'APP版本', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="version-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
