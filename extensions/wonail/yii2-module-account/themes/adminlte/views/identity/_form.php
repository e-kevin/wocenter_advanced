<?php

use wocenter\backend\modules\account\models\Identity;
use wocenter\libs\Constants;
use wocenter\widgets\DateControl;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $form ActiveForm */
/* @var $model Identity */
/* @var $identityGroup array */
/* @var $profiles array */
/* @var $modifyCreatedAt string */
/* @var $modifyUpdatedAt string */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description')->textarea(['rows' => 5]) ?>

<?= $form->field($model, 'identity_group')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])->dropDownList($identityGroup) ?>

<?= $form->field($model, 'profileId')->widget(Select2::className(), [
    'data' => $profiles,
    'options' => [
        'multiple' => true,
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]) ?>

<?= $form->field($model, 'sort_order')->textInput() ?>

<?php
if ($model->modifyCreatedAt) {
    echo $form->field($model, $model->createdAtAttribute)->widget(DateControl::classname());
}
?>

<?php
if ($model->modifyUpdatedAt) {
    echo $form->field($model, $model->updatedAtAttribute)->widget(DateControl::classname());
}
?>

<?= $form->field($model, 'is_invite')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'is_audit')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>