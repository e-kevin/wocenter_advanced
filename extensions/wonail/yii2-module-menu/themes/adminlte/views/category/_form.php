<?php
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\menu\models\MenuCategory */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'id')->textInput(['maxlength' => 64, 'disabled' => $model->isNewRecord ? null : 'disabled']) ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

<?= $form->field($model, 'description')->textarea(['maxlength' => 512]) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>