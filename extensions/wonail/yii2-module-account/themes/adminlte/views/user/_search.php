<?php
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\UserSearch */
/* @var $form ActiveForm */
/* @var $action array */
/* @var $registerTypeList array */
/* @var $genderList array */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]);
?>

<?= $form->field($model, 'id') ?>

<?= $form->field($model, 'username') ?>

<?= $form->field($model, 'email') ?>

<?= $form->field($model, 'created_by')->radioList($registerTypeList) ?>

<?= $form->field($model, 'gender')->radioList($genderList) ?>

<?php ActiveForm::end(); ?>