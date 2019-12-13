<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserTree */

$this->title = 'Create User Tree';
$this->params['breadcrumbs'][] = ['label' => 'User Trees', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-tree-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
