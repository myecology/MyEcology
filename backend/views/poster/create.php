<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Poster */

$this->title = '添加海报';
$this->params['breadcrumbs'][] = ['label' => '海报列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="poster-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
