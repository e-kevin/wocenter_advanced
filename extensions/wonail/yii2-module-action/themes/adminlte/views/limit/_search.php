<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\action\models\ActionLimitSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]); ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'title') ?>

<?= ''//$form->field($model, 'send_notification')->radioList([Constants::UNLIMITED => '不限', 1 => '发送', 0 => '不发送']) ?>

<?= $form->field($model, 'status')->radioList(ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getStatusList())) ?>

<?php ActiveForm::end(); ?>