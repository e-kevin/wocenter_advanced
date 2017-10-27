<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\ExtendProfileSearch */
/* @var $form ActiveForm */
/* @var $action string|array */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]); ?>

<?= $form->field($model, 'profile_name') ?>

<?= $form->field($model, 'visible')->radioList(ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getYesOrNoList())) ?>

<?= $form->field($model, 'status')->radioList(ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getStatusList())) ?>

<?php ActiveForm::end(); ?>