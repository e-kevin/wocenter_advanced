<?php
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\Invite */
/* @var $form ActiveForm */
/* @var $inviteTypeList array */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'invite_type')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'hideSearch' => true,
])->dropDownList($inviteTypeList) ?>

<?= $form->field($model, 'count') ?>

<?= $form->field($model, 'can_num')->textInput()->hint('每个邀请码允许注册用户数')->label('允许注册') ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>