<?php
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\data\models\UserScoreType */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'unit')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'status')->radioList(['禁用', '启用']) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>