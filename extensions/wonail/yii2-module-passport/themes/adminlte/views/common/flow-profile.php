<?php
use wocenter\backend\modules\system\models\Config;
use wocenter\helpers\DateTimeHelper;
use wocenter\helpers\StringHelper;
use wocenter\widgets\DateControl;
use wocenter\backend\modules\passport\models\FlowProfileForm;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var FlowProfileForm $model
 * @var boolean $isFinished
 * @var boolean $canSkip
 */

$this->title = Yii::t('wocenter/app', 'Perfect the personal data');
?>

<?php
$form = ActiveForm::begin([
    'id' => 'step-form',
//    'layout' => 'default',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
            'wrapper' => 'col-sm-9',
            'error' => '',
            'hint' => '',
        ],
    ],
    'box' => false,
//    'enableClientValidation' => false
]);
?>

<?php
foreach ($model->profiles as $profile) {
    echo Html::tag('h5', $profile['profile_name'], [
            'class' => 'text-right',
        ]) . "\n";
    foreach ($profile['extendFieldSettings'] as $k => $field) {
        switch ($field['form_type']) {
            case Config::TYPE_STRING:
                echo $form->field($model, $k)->textInput();
                break;
            case Config::TYPE_TEXT:
                echo $form->field($model, $k)->textarea(['rows' => 4]);
                break;
            case Config::TYPE_RADIO:
                echo $form->field($model, $k)->radioList(StringHelper::parseString($field['form_data']));
                break;
            case Config::TYPE_CHECKBOX:
                echo $form->field($model, $k)->checkboxList(StringHelper::parseString($field['form_data']));
                break;
            case Config::TYPE_SELECT:
                echo $form->field($model, $k)->widget(Select2::classname(), [
                    'options' => [
                        'placeholder' => Yii::t('wocenter/app', 'Please select'),
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                    'hideSearch' => true,
                ])->dropDownList(StringHelper::parseString($field['form_data']));
                break;
            case Config::TYPE_DATE:
                echo $form->field($model, $k)->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATE,
                    'saveFormat' => 'php:Y-m-d',
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'todayHighlight' => true,
                            'endDate' => DateTimeHelper::timeFormat(time()),
                        ]
                    ]
                ]);
                break;
            case Config::TYPE_DATETIME:
                echo $form->field($model, $k)->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_DATETIME,
                ]);
                break;
            case Config::TYPE_TIME:
                echo $form->field($model, $k)->widget(DateControl::className(), [
                    'type' => DateControl::FORMAT_TIME,
                ]);
                break;
        }
    }
}
?>

<div class="form-group">
    <?= Html::submitButton(
        Yii::t('wocenter/app', $isFinished ? 'Finished' : 'Next Step'),
        [
            'class' => 'btn btn-primary btn-lg btn-block',
            'name' => 'submit-button',
        ]
    ); ?>
</div>

<?php ActiveForm::end(); ?>

<?php
if ($canSkip) {
    echo Html::tag('div', Html::button(
        Yii::t('wocenter/app', 'Skip'),
        [
            'class' => 'btn btn-default btn-block',
            'name' => 'skip-button',
            'data' => [
                'method' => 'post',
                'params' => ['skip' => 1],
                'action' => 'step',
            ],
        ]
    ), [
        'class' => 'form-group',
        'style' => 'margin: -10px -15px 15px;',
    ]);
}
?>

<div class="social-auth-links text-center" style="margin: 0 -15px;">
    <p>- OR -</p>
    <div class="form-group">
        <?= Html::a(Yii::t('wocenter/app', 'Logout'), ['/passport/common/logout-on-step'], [
            'class' => 'btn btn-danger btn-block',
            'name' => 'logout-button',
            'data-method' => 'post',
        ]) ?>
    </div>
    <p><?= Yii::t('wocenter/app', "We've been doing better for you!"); ?></p>
</div>