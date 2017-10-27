<?php
use wocenter\backend\modules\system\models\ConfigSearch;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model ConfigSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
/* @var $configGroupList array */
/* @var $configTypeList array */
/* @var $statusList array */
?>

<?php

$form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]);
?>

<?= $form->field($model, 'name')->hint(false) ?>

<?= $form->field($model, 'title')->hint(false) ?>

<?= $form->field($model, 'category_group')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'hideSearch' => true,
])->dropDownList($configGroupList)->hint(false)
?>

<?= $form->field($model, 'type')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'hideSearch' => true,
])->dropDownList($configTypeList)->hint(false)
?>

<?= $form->field($model, 'status')->radioList($statusList)->hint(false)
?>

<?php ActiveForm::end(); ?>