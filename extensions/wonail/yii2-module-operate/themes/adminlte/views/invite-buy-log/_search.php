<?php
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteBuyLogSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]); ?>

<?= $form->field($model, 'id') ?>

<?= $form->field($model, 'invite_type') ?>

<?= $form->field($model, 'uid') ?>

<?= $form->field($model, 'num') ?>

<?= $form->field($model, 'content') ?>

<?php ActiveForm::end(); ?>