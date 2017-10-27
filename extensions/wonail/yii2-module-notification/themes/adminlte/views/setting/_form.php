<?php
use wocenter\backend\modules\notification\models\Notify;
use wocenter\libs\Constants;
use wocenter\Wc;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model Notify */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'node')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'content_key')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'name_key')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'email_sender')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'hideSearch' => true,
])->dropDownList(Wc::$service->getSystem()->getConfig()->extra('EMAIL_SENDER')) ?>

<?= $form->field($model, 'send_message')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'sender')->radioList($model->getSenderList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>