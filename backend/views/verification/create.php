<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Verification */

$this->title = 'Create Verification';
$this->params['breadcrumbs'][] = ['label' => 'Verifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="verification-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
