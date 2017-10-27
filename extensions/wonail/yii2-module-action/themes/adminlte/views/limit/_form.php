<?php
use wocenter\helpers\DateTimeHelper;
use wocenter\libs\Constants;
use wocenter\backend\modules\action\models\ActionLimit;
use wocenter\widgets\DateControl;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model ActionLimit */
/* @var $form ActiveForm */
/* @var $actionList array */
/**
 * @var string $inline_input_group
 *
 * @param $form ActiveForm
 * @param $model ActionLimit
 * @param $attribute string
 * @param $list array
 *
 * @return string
 */
$inline_input_group = function ($form, $model, $attribute, $list) {
    $content = Html::tag('div',
        $form->field($model, $attribute, [
            'template' => '{input}',
            'options' => [
                'class' => 'form-group',
                'style' => 'margin:0;',
            ],
        ])->widget(Select2::className(), [
            'hideSearch' => true,
        ])->dropDownList($list, [], false),
        [
            'style' => "width:10%;vertical-align:middle;display:table-cell;",
        ]
    );

    return Html::tag('div', '{input} ' . $content, [
        'class' => 'input-group',
        'style' => 'width:100%',
    ]);
};
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'frequency')->textInput() ?>

<?= $form->field($model, 'timestamp', [
    'inputTemplate' => $inline_input_group(
        $form,
        $model,
        'time_unit',
        DateTimeHelper::getTimeUnitList()
    ),
])->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'punish')->checkboxList(ActionLimit::$punishList) ?>

<?= $form->field($model, 'action')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose')
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'hideSearch' => true,
])->dropDownList($actionList) ?>

<?= ''//$form->field($model, 'send_notification')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'check_ip')->radioList(Constants::getYesOrNoList()) ?>

<?= ''
//$form->field($model, 'send_message')->textarea([
//    'rows' => 8,
//])
?>

<?= $form->field($model, 'remind_message')->textarea([
    'rows' => 8,
    'placeholder' => Yii::t('wocenter/app', 'In {next_action_time} you can also perform the operation {surplus_number} times!'),
]) ?>

<?= $form->field($model, 'warning_message')->textarea([
    'rows' => 8,
    'placeholder' => Yii::t('wocenter/app', 'Frequent operation: Please do this after {next_action_time}.'),
]) ?>

<?= $form->field($model, 'finish_message')->textarea(['rows' => 8]) ?>

<?php
if ($model->modifyUpdatedAt) {
    echo $form->field($model, $model->updatedAtAttribute)->widget(DateControl::classname());
}
?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>