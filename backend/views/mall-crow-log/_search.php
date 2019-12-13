<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\MallCrowLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mall-crow-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'crow_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'crow_name') ?>

    <?= $form->field($model, 'number') ?>

    <?php // echo $form->field($model, 'pay_symbol') ?>

    <?php // echo $form->field($model, 'symbol') ?>

    <?php // echo $form->field($model, 'pay_number') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'release_at') ?>

    <?php // echo $form->field($model, 'release_cycle') ?>

    <?php // echo $form->field($model, 'release_times') ?>

    <?php // echo $form->field($model, 'each_release') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'release_end_at') ?>

    <?php // echo $form->field($model, 'mall_crow_id') ?>

    <?php // echo $form->field($model, 'is_release') ?>

    <?php // echo $form->field($model, 'time') ?>

    <?php // echo $form->field($model, 'flag') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
