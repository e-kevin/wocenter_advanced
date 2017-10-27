<?php
use wocenter\backend\modules\account\models\IdentityGroup;
use wocenter\widgets\DateControl;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IdentityGroup */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?php
if ($model->modifyUpdatedAt) {
    echo $form->field($model, $model->updatedAtAttribute)->widget(DateControl::classname());
}
?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>