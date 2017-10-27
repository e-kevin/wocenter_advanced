<?php

use wocenter\libs\Utils;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var yii\web\View $this
 * @var \yii\bootstrap\ActiveForm $form
 * @var \wocenter\backend\modules\passport\models\PassportForm $model
 */

$this->title = Yii::t('wocenter/app', 'Reset Password');
?>

<?php $form = ActiveForm::begin(['id' => 'resetpassword-form', 'enableClientValidation' => true]); ?>

<?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('wocenter/app', 'Please enter a new password')])->label(false) ?>

<?= $form->field($model, 'passwordRepeat')->passwordInput(['placeholder' => Yii::t('wocenter/app', 'Please enter a new password again')])->label(false) ?>

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
        ],
    ])->label(false);
}
?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('wocenter/app', 'Reset'), ['class' => 'btn btn-primary btn-block btn-lg', 'name' => 'submit-button']) ?>
</div>

<?php ActiveForm::end(); ?>

<div class="social-auth-links text-center">
    <p>- OR -</p>
    <div class="form-group">
        <?= Html::a(Yii::t('wocenter/app', 'Login Now'), ['/passport/common/login'], ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
        <?= Html::a(Yii::t('wocenter/app', 'Signup Now'), ['/passport/common/signup'], ['class' => 'btn btn-danger btn-block', 'name' => 'signup-button']) ?>
    </div>
    <p><?= Yii::t('wocenter/app', "We've been doing better for you!"); ?></p>
</div>

<?php $this->registerJs(<<<JS
$("#{$form->id}").find("input").get(1).focus();
JS
, View::POS_END); ?>
