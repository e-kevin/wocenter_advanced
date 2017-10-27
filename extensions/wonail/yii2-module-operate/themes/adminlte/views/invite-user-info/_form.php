<?php
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteUserInfo */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'invite_type')->textInput() ?>

<?= $form->field($model, 'uid')->textInput() ?>

<?= $form->field($model, 'num')->textInput() ?>

<?= $form->field($model, 'already_num')->textInput() ?>

<?= $form->field($model, 'success_num')->textInput() ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>