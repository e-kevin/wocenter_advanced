<?php
use wocenter\libs\Utils;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var \wocenter\backend\modules\passport\models\LoginForm $model
 */

$this->title = Yii::t('wocenter/app', 'Login');
?>
<?php
$form = ActiveForm::begin([
    'id' => 'login-form',
//    'enableClientValidation' => false
]);
?>

<?= $form->field($model, 'identity')->textInput(['placeholder' => $model->getAttributeLabel('identity')])->label(false) ?>

<?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>

<?php
if (Utils::showVerify()) {
    echo $form->field($model, 'captcha')->widget(Captcha::className(), [
        'options' => [
            'class' => 'form-control',
            'placeholder' => $model->getAttributeLabel('captcha'),
        ],
        'imageOptions' => [
            'id' => 'verifycode-image',
            'title' => Yii::t('wocenter/app', 'Change another one'),
            'height' => '20px',
            'width' => '100px',
            'alt' => $model->getAttributeLabel('captcha'),
            'style' => 'cursor:pointer',
        ],
    ])->label(false);
}
?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('wocenter/app', 'Login'), ['class' => 'btn btn-primary btn-block btn-lg', 'name' => 'login-button']) ?>
    <?= Html::hiddenInput(Yii::$app->params['redirect'], $returnUrl) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="social-auth-links text-center">
    <p>- OR -</p>
    <div class="form-group btn-group width-full">
        <?= Html::a(Html::tag('span', Yii::t('wocenter/app', 'Signup Now'), ['class' => 'col-sm-offset-1 col-xs-offset-2']), ['/passport/common/signup'], ['class' => 'btn btn-success col-xs-10 col-sm-11', 'name' => 'signup-button']) ?>
        <?=
        Html::button('<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>', [
            'class' => 'btn btn-success col-xs-2 col-sm-1',
            'data-toggle' => 'collapse',
            'data-target' => '#btn-group',
            'aria-expanded' => false,
        ])
        ?>
    </div>
    <div id="btn-group" class="list-group collapse" style="margin-top: -15px;">
        <?= Html::a(Yii::t('wocenter/app', 'Find Password'), ['/passport/security/find-password'], ['name' => 'findpassword-button', 'class' => 'text-center list-group-item']) ?>
        <?= Html::a(Yii::t('wocenter/app', 'Activate Account'), ['/passport/security/activate-account'], ['name' => 'send-button', 'class' => 'text-center list-group-item']) ?>
    </div>

    <p class="m-t-5"><?= Yii::t('wocenter/app', "We've been doing better for you!"); ?></p>
</div>

<?php
$this->registerJs(<<<JS
$("#{$form->id}").find("input").get(1).focus();
JS
, View::POS_END);
?>
