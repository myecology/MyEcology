<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\InvitePoolSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invite-pool-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'currency_id') ?>

    <?= $form->field($model, 'symbol') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'amount_left') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'expired_at') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'prize') ?>

    <?php // echo $form->field($model, 'prize_registerer') ?>

    <?php // echo $form->field($model, 'prize_inviter') ?>

    <?php // echo $form->field($model, 'prize_grand_inviter') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
