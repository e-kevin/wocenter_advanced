<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\IdentitySearch */
/* @var $form ActiveForm */
/* @var $action array|string */
/* @var $identityGroup array */

$yesOrNoList = ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getYesOrNoList());
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'method' => 'get',
    'action' => $action,
    'box' => false,
]); ?>

<?= $form->field($model, 'title') ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'identity_group')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'dropdownParent' => new \yii\web\JsExpression("$('#{$form->id}')"),
    ],
])->dropDownList($identityGroup) ?>

<?= $form->field($model, 'is_invite')->radioList($yesOrNoList) ?>

<?= $form->field($model, 'is_audit')->radioList($yesOrNoList) ?>

<?= $form->field($model, 'status')->radioList(ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getStatusList())) ?>

<?php ActiveForm::end(); ?>