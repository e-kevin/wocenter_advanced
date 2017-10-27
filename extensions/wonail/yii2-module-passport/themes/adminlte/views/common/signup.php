<?php
use wocenter\libs\Utils;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\ToggleButtonGroup;
use yii\captcha\Captcha;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var ActiveForm $form
 * @var \wocenter\backend\modules\passport\models\SignupForm $model
 * @var array $registerTypeTextTabList 系统开放的注册方式
 * @var boolean $showInviteRegisterType 显示邀请注册方式
 * @var boolean $isOpenEmailVerify 开启邮箱验证
 * @var boolean $isOpenMobileVerify 开启手机验证
 */

$this->title = Yii::t('wocenter/app', 'Signup');
$getCodeText = Yii::t('wocenter/app', 'Get verification code');
$retrieveText = Yii::t('wocenter/app', 'Retrieve');
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <?php
        foreach ($registerTypeTextTabList as $id => $type) {
            if ($id == $model->getRecommendRegisterType()) {
                echo "<li><a data-toggle='tab' href=\"#tab_{$id}\">{$type}" . Yii::t('wocenter/app', 'Signup') .
                    "<sup class='label bg-red' style='top: -8px;left: 2px'>" . Yii::t('wocenter/app', 'Recommend') . "</sup></a></li>";
            } else {
                echo "<li><a data-toggle='tab' href=\"#tab_{$id}\">{$type}" . Yii::t('wocenter/app', 'Signup') . "</a></li>";
            }
        }
        if ($showInviteRegisterType) {
            echo Html::tag('li', Html::a(Yii::t('wocenter/app', 'Invite Signup'), ['/passport/common/invite-signup']));
        }
        ?>
    </ul>
    <?php
    $form = ActiveForm::begin([
        'id' => 'signup-form',
//        'enableClientValidation' => false
    ]);
    ?>
    <div class="tab-content p-t-10">
        <?php
        foreach ($registerTypeTextTabList as $id => $type) {
            $input = [];
            $options = [
                'placeholder' => $model->getAttributeLabel($model->registerTypeList[$id])
            ];
            switch ($model->registerTypeList[$id]) {
                case 'email':
                    // 开启邮箱验证
                    if ($isOpenEmailVerify) {
                        $input[2] = $form->field($model, 'emailVerifyCode', [
                            'inputTemplate' => Html::tag('div', '{input} ' .
                                Html::tag('span', $getCodeText, [
                                    'class' => 'input-group-addon',
                                    'data-role' => 'sendVerify',
                                    'data-register-type' => $id,
                                    'role' => 'button'
                                ]), ['class' => 'input-group'])
                        ])->textInput(['placeholder' => Yii::t('wocenter/app', 'Email Code')])->label(false);
                    }
                    $options['title'] = '用于接收系统通知、密码重置等用途';
                    break;
                case 'mobile':
                    // 开启手机号验证
                    if ($isOpenMobileVerify) {
                        $input[2] = $form->field($model, 'mobileVerifyCode', [
                            'inputTemplate' => Html::tag('div', '{input} ' .
                                Html::tag('span', $getCodeText, [
                                    'class' => 'input-group-addon',
                                    'data-role' => 'sendVerify',
                                    'data-register-type' => $id,
                                    'role' => 'button'
                                ]), ['class' => 'input-group'])
                        ])->textInput(['placeholder' => Yii::t('wocenter/app', 'Mobile Code')])->label(false);
                    }
                    break;
            }
            $input[0] = $form->field($model, $model->registerTypeList[$id])->textInput($options)->label(false);
            $input[1] = Html::activeHiddenInput($model, 'registerType', ['value' => $id]);
            ksort($input);
            echo Html::tag('div', implode('', $input), [
                'id' => 'tab_' . $id,
                'class' => 'tab-pane',
            ]);
        }
        ?>

        <?=
        $form->field($model, 'password', [
            'inputTemplate' => Html::tag('div', '{input}' . Html::tag('span', '<i class="fa fa-eye-slash"></i>', [
                'class' => 'input-group-addon',
                'style' => 'cursor:pointer',
                'onclick' => 'changeShow(this)'
            ]), ['class' => 'input-group']),
        ])->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false)
        ?>

        <?= $form->field($model, 'passwordRepeat')->passwordInput(['placeholder' => $model->getAttributeLabel('passwordRepeat')])->label(false) ?>

        <?php
        if (Utils::showVerify()) {
            echo $form->field($model, 'captcha')->widget(Captcha::className(), [
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => $model->getAttributeLabel('captcha')
                ],
                'imageOptions' => [
                    'id' => 'verifycode-image',
                    'title' => Yii::t('wocenter/app', 'Change another one'),
                    'height' => '20px',
                    'width' => '100px',
                    'alt' => $model->getAttributeLabel('captcha'),
                    'style' => 'cursor:pointer',
                ]
            ])->label(false);
        }

        if (isset($registerIdentityList)) {
            echo $form->field($model, 'registerIdentity')->widget(ToggleButtonGroup::classname(), [
                'items' => $registerIdentityList,
                'labelOptions' => [
                    'class' => 'btn-default',
                ],
                'type' => 'radio',
            ])->label(false);
        }
        ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('wocenter/app', 'Signup'), ['class' => 'btn btn-primary btn-block btn-lg', 'name' => 'register-button']) ?>
            <?= Html::activeHiddenInput($model, 'code', ['value' => Yii::$app->getRequest()->getQueryParam('code')]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <!-- /.tab-content -->
</div>

<div class="social-auth-links text-center">
    <p>- OR -</p>
    <div class="form-group btn-group width-full">
        <?= Html::a(Html::tag('span', Yii::t('wocenter/app', 'Login'), ['class' => 'col-sm-offset-1 col-xs-offset-2']), ['/passport/common/login'], ['class' => 'btn btn-success col-xs-10 col-sm-11', 'name' => 'login-button']) ?>
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
        <?= Html::a(Yii::t('wocenter/app', 'Activate Account'), ['/passport/security/activate-account'], ['name' => 'send-button', 'class' => 'text-center list-group-item']) ?>
    </div>

    <p class="m-t-5"><?= Yii::t('wocenter/app', "We've been doing better for you!"); ?></p>
</div>

<?php
$sendVerifyUrl = Url::toRoute(['/passport/security/send-verify']);
$surplusTime = 60;
$registerType = Html::getInputName($model, 'registerType');
$emailText = Yii::t('wocenter/app', 'You must fill in the email to receive the verification code.');
$mobileText = Yii::t('wocenter/app', 'You must fill in the mobile to receive the verification code.');
$passwordId = Html::getInputId($model, 'password');
$this->registerJs(<<<JS
var emailTimeout,
    mobileTimeout,
    emailVerifyCookieName = 'emailVerifyRemind',
    mobileVerifyCookieName = 'mobileVerifyRemind';
// 设置测试时间
//wn.cookie.set(emailVerifyCookieName, 10, 10);
//wn.cookie.set(mobileVerifyCookieName, 120, 120);
// 获取页面所有`获取验证码`按钮
sendVerifyBtn = $(".tab-pane").find("[data-role=sendVerify]");
// 开始倒计时
sendVerifyBtn.each(function() {
    SurplusTime($(this), $(this).data('register-type'));
});
// 激活当前标签的输入控件
$(document).on("shown.bs.tab", "a[data-toggle=tab]", function() {
    $(".tab-pane").find("input").attr("disabled",true);
    // 当前激活的Tab标签
    currentTabPane = $(".tab-pane").eq($(".nav.nav-tabs li a").index(this));
    // 激活输入控件
    currentTabPane.find("input").attr("disabled",false);
    currentTabPane.find("input").get(0).focus();
});
// 激活系统推荐注册方式
$(".nav.nav-tabs li a[href='#tab_{$model->getRecommendRegisterType()}']").click();
// 倒计时
function SurplusTime(obj, registerType) {
    registerType = parseInt(registerType);
    var secondsRemind, verifyCookieName;
    switch (registerType) {
        case 1:
            secondsRemind = wn.cookie.get(emailVerifyCookieName);
            verifyCookieName = emailVerifyCookieName;
        break;
        case 2:
            secondsRemind = wn.cookie.get(mobileVerifyCookieName);
            verifyCookieName = mobileVerifyCookieName;
        break;
    }
    if (secondsRemind == undefined || secondsRemind == 0) {
        switch (registerType) {
            case 1:
                emailTimeout && clearTimeout(emailTimeout);
            break;
            case 2:
                mobileTimeout && clearTimeout(mobileTimeout);
            break;
        }
        obj.text("{$getCodeText}");
        obj.removeAttr("disabled");
        obj.removeClass("disabled");
    } else {
        obj.attr("disabled", true);
        obj.addClass("disabled");
        obj.text("{$retrieveText}(" + secondsRemind + ")");
        secondsRemind--;
        wn.cookie.set(verifyCookieName, secondsRemind, secondsRemind+1);
        switch (registerType) {
            case 1:
                emailTimeout = setTimeout(function() {SurplusTime(obj, 1);}, 1000);
            break;
            case 2:
                mobileTimeout = setTimeout(function() {SurplusTime(obj, 2);}, 1000);
            break;
        }
    }
}
// 发送验证码
$("[data-role=sendVerify]").click(function(e) {
    e.preventDefault();
    var \$this = $(this);
    if (\$this.hasClass('disabled')) {
        return false;
    }
    var identity, message, registerType = \$this.parents('.tab-pane').find('[name="{$registerType}"]').val();
    switch (registerType) {
        case '1':
            identity = \$this.parents('.tab-pane').find('[name="SignupForm[email]"]').val();
            message = "{$emailText}";
            verifyCookieName = emailVerifyCookieName;
            break;
        case '2':
            identity = \$this.parents('.tab-pane').find('[name="SignupForm[mobile]"]').val();
            message = "{$mobileText}";
            verifyCookieName = mobileVerifyCookieName;
        break;
    }
    if (identity == '' || identity == undefined) {
        errorDialog.alert(message);
        return false;
    }
    // 发送验证码
    $.post("{$sendVerifyUrl}", {identity: identity}, function(data) {
        if (data.status) {
            wn.cookie.set(verifyCookieName, {$surplusTime}, {$surplusTime}+1);
            SurplusTime(\$this, registerType);
        }
        successDialog.alert(data.message);
    }, 'json');
});
// 显示密码
function changeShow(obj) {
    var button = $(obj).find('i').context.firstChild;
    if (button.className == 'fa fa-eye') {
        $(button).removeClass("fa-eye");
        $(button).addClass("fa-eye-slash");
        $("#{$passwordId}").attr('type', 'password');
    } else {
        $(button).removeClass("fa-eye-slash");
        $(button).addClass("fa-eye");
        $("#{$passwordId}").attr('type', 'text');
    }
}
JS
, View::POS_END);
?>