<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\action\models\ActionSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
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

<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'type')->radioList(ArrayHelper::merge(
    [Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')],
    $model->typeList)
)->hint(false) ?>

<?= $form->field($model, 'status')->radioList(ArrayHelper::merge(
    [Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')],
    Constants::getStatusList())
)->hint(false) ?>

<?php ActiveForm::end(); ?>