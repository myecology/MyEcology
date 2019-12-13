<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CrowdfundingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crowdfunding-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'income_img') ?>

    <?= $form->field($model, 'start_time') ?>

    <?= $form->field($model, 'end_time') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'release_type') ?>

    <?php // echo $form->field($model, 'release_start_at') ?>

    <?php // echo $form->field($model, 'release_end_at') ?>

    <?php // echo $form->field($model, 'release_cycle') ?>

    <?php // echo $form->field($model, 'mall_symbol') ?>

    <?php // echo $form->field($model, 'mall_proportion') ?>

    <?php // echo $form->field($model, 'exchange_symbol') ?>

    <?php // echo $form->field($model, 'exchange_num') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <?php // echo $form->field($model, 'min_buy') ?>

    <?php // echo $form->field($model, 'exchange_total') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
