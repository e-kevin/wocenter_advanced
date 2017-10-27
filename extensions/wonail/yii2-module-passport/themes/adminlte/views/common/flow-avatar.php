<?php
use wocenter\backend\modules\passport\models\FlowProfileForm;
use wonail\adminlte\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var FlowProfileForm $model
 * @var boolean $isFinished
 * @var boolean $canSkip
 */

$this->title = Yii::t('wocenter/app', 'Change avatar');
?>

<?php
$form = ActiveForm::begin([
    'id' => 'step-form',
    'fieldConfig' => [
        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
            'wrapper' => 'col-sm-9',
            'error' => '',
            'hint' => '',
        ],
    ],
    'box' => false
//    'enableClientValidation' => false
]);
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
        'style' => 'margin:-10px -15px 15px',
    ]);
}
?>

<div class="social-auth-links text-center" style="margin: 0 -15px;">
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
