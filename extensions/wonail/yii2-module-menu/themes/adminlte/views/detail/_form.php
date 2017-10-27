<?php
use wocenter\backend\modules\menu\models\Menu;
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model Menu */
/* @var $form ActiveForm */
/* @var $menuList array */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'parent_id')->widget(Select2::className())
    ->dropDownList($menuList, [
        'encode' => false,
        'prompt' => [
            'text' => Yii::t('wocenter/app', 'Root level'),
            'options' => [
                'value' => '0',
            ],
        ],
    ]);
?>

<?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

<?= $form->field($model, 'alias_name')->textInput(['maxlength' => 64]) ?>

<?= $form->field($model, 'icon_html')->textInput(['maxlength' => 30]) ?>

<?= $form->field($model, 'url')->textInput(['maxlength' => 512]) ?>

<?= $form->field($model, 'description')->textarea(['maxlength' => 512]) ?>

<?= $form->field($model, 'sort_order')->textInput() ?>

<?= $form->field($model, 'target')->radioList(Constants::getOpenTargetList()) ?>

<?= $form->field($model, 'is_dev')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'show_on_menu')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>