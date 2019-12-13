<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\NodeWtSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="node-wt-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'income') ?>

    <?= $form->field($model, 'alte_price') ?>

    <?= $form->field($model, 'alte_symbol') ?>

    <?php // echo $form->field($model, 'alte_number') ?>

    <?php // echo $form->field($model, 'alte_rules') ?>

    <?php // echo $form->field($model, 'total _awards') ?>

    <?php // echo $form->field($model, 'reward_symbol') ?>

    <?php // echo $form->field($model, 'super_number') ?>

    <?php // echo $form->field($model, 'super_factor') ?>

    <?php // echo $form->field($model, 'super_rules') ?>

    <?php // echo $form->field($model, 'super_price') ?>

    <?php // echo $form->field($model, 'super_explain') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
