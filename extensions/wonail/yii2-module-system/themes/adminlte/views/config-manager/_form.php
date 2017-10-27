<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $form ActiveForm */
/* @var $model \wocenter\backend\modules\system\models\Config */
/* @var $configGroupList array */
/* @var $configTypeList array */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput() ?>

<?= $form->field($model, 'title')->textInput() ?>

<?= $form->field($model, 'sort_order')->textInput() ?>

<?= $form->field($model, 'type')->widget(Select2::className(), [
    'hideSearch' => true,
])->dropDownList($configTypeList, [], false) ?>

<?= $form->field($model, 'category_group')->widget(Select2::className(), [
    'hideSearch' => true,
])->dropDownList($configGroupList, [], false) ?>

<?= $form->field($model, 'value')->textarea(['rows' => 4]) ?>

<?= $form->field($model, 'extra')->textarea(['rows' => 4]) ?>

<?= $form->field($model, 'remark')->textarea(['rows' => 4]) ?>

<?= $form->field($model, 'rule')->textarea(['rows' => 10]) ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>
