<?php

use wocenter\backend\modules\log\models\UserScoreLogSearch;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model UserScoreLogSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
/* @var $typeList array 积分类型列表*/
/* @var $actionList array 调整类型列表*/
?>

<?php
$form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]);
?>

<?= $form->field($model, 'uid') ?>

<?= $form->field($model, 'ip') ?>

<?= $form->field($model, 'type')->radioList($typeList) ?>

<?= $form->field($model, 'action')->radioList($actionList) ?>

<?php ActiveForm::end(); ?>