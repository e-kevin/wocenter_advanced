<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\Rank */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?='';// $form->field($model, 'logo')->textInput() ?>

<?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

<?=''// $form->field($model, 'label_color')->textInput(['maxlength' => true]) ?>

<?=''// $form->field($model, 'label_bg')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'allow_user_apply')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>