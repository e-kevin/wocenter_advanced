<?php
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\data\models\Tag */
/* @var $form ActiveForm */
/* @var $tagList array 标签列表 */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'parent_id')->widget(Select2::className())
    ->dropDownList($tagList, [
        'encode' => false,
        'prompt' => [
            'text' => Yii::t('wocenter/app', 'Root level'),
            'options' => [
                'value' => '0',
            ],
        ],
    ]);
?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'sort_order')->textInput() ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>