<?php
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteLogSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
/* @var $inviteTypeList array */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]); ?>

<?= $form->field($model, 'invite_type_id')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'dropdownParent' => new JsExpression("$('#{$form->id}')"),
    ],
    'hideSearch' => true,
])->dropDownList($inviteTypeList) ?>

<?= $form->field($model, 'uid') ?>

<?= $form->field($model, 'inviter_id') ?>

<?php ActiveForm::end(); ?>