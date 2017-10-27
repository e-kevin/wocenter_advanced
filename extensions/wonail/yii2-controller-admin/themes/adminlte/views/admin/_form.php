<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\BackendUser */
/* @var $form ActiveForm */
/* @var $showStatus boolean */
?>

<?php $form = ActiveForm::begin(); ?>

<?php
if ($model->getScenario() !== $model::SCENARIO_UPDATE) {
    echo $form->field($model, 'user_id')->textInput();
}
?>

<?php
if ($showStatus) {
    echo $form->field($model, 'status')->radioList(Constants::getStatusList());
}
?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>