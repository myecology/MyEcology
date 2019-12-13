<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\Models\search\WithdrawSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="setting-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>
    <?= $form->field($model, 'wallet_id') ?>
    <?= $form->field($model, 'address') ?>
    <?= $form->field($model, 'symbol')->dropDownList(\common\models\Currency::loadList(), ['prompt' => '选择币种']) ?>
    <?= $form->field($model, 'status')->dropDownList(\common\models\Withdraw::$lib_status, ['prompt' => '选择状态']) ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
