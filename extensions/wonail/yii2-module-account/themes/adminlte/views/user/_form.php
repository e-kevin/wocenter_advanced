<?php
use wocenter\backend\modules\account\models\BaseUser;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model BaseUser */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'status')->radioList([
    BaseUser::STATUS_FORBIDDEN => Yii::t('wocenter/app', 'User disabled status'),
    BaseUser::STATUS_ACTIVE => Yii::t('wocenter/app', 'User active status'),
]) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>