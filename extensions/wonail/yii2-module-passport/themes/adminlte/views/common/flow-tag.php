<?php
use wonail\adminlte\widgets\Select2;
use wocenter\backend\modules\passport\models\FlowTagForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var FlowTagForm $model
 * @var boolean $isFinished
 * @var boolean $canSkip
 */

$this->title = Yii::t('wocenter/app', 'Add label');
?>

<?php
$form = ActiveForm::begin([
    'id' => 'step-form',
//    'enableClientValidation' => false
]);
?>

<?=
$form->field($model, 'tag')->widget(Select2::classname(), [
    'data' => $model->tagList,
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please select label'),
        'multiple' => 'multiple',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])->label(false)
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
        'style' => 'margin-top:-10px',
    ]);
}
?>

<div class="social-auth-links text-center">
    <p>- OR -</p>
    <div class="form-group">
        <?= Html::a(Yii::t('wocenter/app', 'Logout'), ['/passport/common/logout-on-step'], [
            'class' => 'btn btn-danger btn-block',
            'name' => 'signup-button',
            'data-method' => 'post',
        ]) ?>
    </div>
    <p><?= Yii::t('wocenter/app', "We've been doing better for you!"); ?></p>
</div>