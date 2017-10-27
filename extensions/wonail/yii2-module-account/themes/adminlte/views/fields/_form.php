<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\ExtendFieldSetting */
/* @var $form ActiveForm */
/* @var $formTypeList array */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'field_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'form_type')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'hideSearch' => true
])->dropDownList($formTypeList)
?>

<?= $form->field($model, 'default_value')->textInput() ?>

<?= $form->field($model, 'form_data')->textarea(['rows' => 5]) ?>

<?= $form->field($model, 'rule')->textarea(['rows' => 5]) ?>

<?= $form->field($model, 'hint')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'sort_order')->textInput() ?>

<?= $form->field($model, 'visible')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>