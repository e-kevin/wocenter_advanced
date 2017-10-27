<?php
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $model \wocenter\backend\modules\data\models\AreaRegion */
/* @var $form ActiveForm */
/* @var array $areaSelectList */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'parent_id')->widget(Select2::className())
    ->dropDownList($areaSelectList, [
        'encode' => false,
        'prompt' => [
            'text' => Yii::t('wocenter/app', 'Root level'),
            'options' => [
                'value' => '0',
            ],
        ],
    ]);
?>

<?= $form->field($model, 'region_name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'region_type')->widget(Select2::className())
    ->dropDownList($model->getRegionTypeList(), [], false);
?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>