<?php
use wocenter\libs\Constants;
use wocenter\widgets\DateControl;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\action\models\Action */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

<?= $form->field($model, 'rule')->textarea(['rows' => 4]) ?>

<?= $form->field($model, 'type')->radioList($model->typeList) ?>

<?php
if ($model->modifyUpdatedAt) {
    echo $form->field($model, $model->updatedAtAttribute)->widget(DateControl::classname());
}
?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>