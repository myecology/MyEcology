<?php
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']])?>

<input type="file" name="image">

<button>Submit</button>

<?php ActiveForm::end()?>