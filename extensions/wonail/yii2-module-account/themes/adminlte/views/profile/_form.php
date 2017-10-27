<?php
use wocenter\libs\Constants;
use wocenter\widgets\DateControl;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\ExtendProfile */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'profile_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'sort_order')->textInput() ?>

<?php
if ($model->modifyCreatedAt) {
    echo $form->field($model, $model->createdAtAttribute)->widget(DateControl::classname());
}
?>

<?= $form->field($model, 'visible')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>