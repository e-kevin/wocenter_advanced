<?php
use wocenter\backend\modules\log\models\ActionLogSearch;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model ActionLogSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
/* @var $actionSelectList array */
/* @var $actionTypeList array */
?>

<?php
$form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]);
?>

<?=
$form->field($model, 'action_id')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'dropdownParent' => new JsExpression("$('#{$form->id}')"),
    ],
    'hideSearch' => true,
])->dropDownList($actionSelectList)
?>

<?= $form->field($model, 'user_id') ?>

<?= $form->field($model, 'action_ip') ?>

<?= $form->field($model, 'created_type')->radioList($actionTypeList) ?>

<?php ActiveForm::end(); ?>