<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var \wocenter\backend\modules\passport\models\LoginForm $model */

use wocenter\libs\Utils;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="passport-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'identity')->textInput([
                'placeholder' => $model->getAttributeLabel('identity'),
                'autofocus' => true,
            ])->label(false) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>

            <?php
            if (Utils::showVerify()) {
                echo $form->field($model, 'captcha')->widget(Captcha::className(), [
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => $model->getAttributeLabel('captcha'),
                    ],
                    'template' => '<div class="input-group">{input}<div class="input-group-addon" style="padding:0;">{image}</div></div>',
                    'imageOptions' => [
                        'id' => 'verifycode-image',
                        'title' => Yii::t('wocenter/app', 'Change another one'),
                        'height' => '30px',
                        'width' => '100px',
                        'alt' => $model->getAttributeLabel('captcha'),
                        'style' => 'cursor:pointer',
                    ],
                ])->label(false);
            }
            ?>

            <div style="color:#999;margin:1em 0">
                If you forgot your password you can <?= Html::a('reset it', ['/passport/security/find-password']) ?>.
            </div>

            <div class="form-group">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
