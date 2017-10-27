<?php
use wocenter\helpers\DateTimeHelper;
use wocenter\libs\Constants;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteType */
/* @var $form ActiveForm */
/* @var $scoreList array 积分列表 */
/* @var $inviteIdentityList array 可邀请注册的身份列表 */
/**
 * @var string $inline_input_group
 *
 * @param $form ActiveForm
 * @param $model \wocenter\backend\modules\operate\models\InviteType
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

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'length')->textInput() ?>

<?= $form->field($model, 'expired_at', [
    'inputTemplate' => $inline_input_group(
        $form,
        $model,
        'expired_time_unit',
        DateTimeHelper::getTimeUnitList()
    ),
])->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'cycle_time', [
    'inputTemplate' => $inline_input_group(
        $form,
        $model,
        'cycle_time_unit',
        DateTimeHelper::getTimeUnitList()
    ),
])->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'cycle_num', [
    'inputTemplate' => Html::tag('div', '{input}<span class="input-group-addon">个</span>', ['class' => 'input-group'])
])->textInput()->label('周期内可购买') ?>

<?= $form->field($model, 'pay_score', [
    'inputTemplate' => $inline_input_group(
        $form,
        $model,
        'pay_score_type',
        $scoreList
    ),
])->textInput() ?>

<?= $form->field($model, 'increase_score', [
    'inputTemplate' => $inline_input_group(
        $form,
        $model,
        'increase_score_type',
        $scoreList
    ),
])->textInput() ?>

<?= $form->field($model, 'identities')->widget(Select2::className(), [
    'data' => $inviteIdentityList,
    'options' => [
        'multiple' => true,
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]) ?>

<?='';// $form->field($model, 'auth_groups')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'each_follow')->radioList(Constants::getYesOrNoList()) ?>

<?= $form->field($model, 'status')->radioList(Constants::getStatusList()) ?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>