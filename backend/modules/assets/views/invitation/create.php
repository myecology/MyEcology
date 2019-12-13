<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\assets\models\Invitation */

$this->title = 'Create Invitation';
$this->params['breadcrumbs'][] = ['label' => 'Invitations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitation-create">

    <div class="box box-success">
        <div class="box-body">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
    

</div>
