<?php
use wonail\adminlte\widgets\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\data\models\AreaRegionSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]); ?>

<?= $form->field($model, 'region_name') ?>

<?= Html::activeHiddenInput($model, 'parent_id') ?>

<?php ActiveForm::end(); ?>