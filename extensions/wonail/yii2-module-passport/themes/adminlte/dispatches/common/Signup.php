<?php

namespace wocenter\backend\modules\passport\themes\adminlte\dispatches\common;

use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SignupForm;
use wocenter\Wc;
use Yii;
use yii\helpers\Url;

/**
 * Class Signup
 */
class Signup extends Dispatch
{
    
    /**
     * @param integer $code 邀请码
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run($code = 0)
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->controller->goHome();
        }
        
        $model = new SignupForm(['scenario' => SignupForm::SCENARIO_SIGNUP]);
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->signup()) {
                $data = $this->afterSignup();
                $this->success($data['message'], $data['url'], 3);
            } else {
                $this->error($model->message, '', 2);
            }
        }
        
        $this->_assign($model, ['code' => $code]);
        
        return $this->display();
    }
    
    /**
     * 模板赋值
     *
     * @param SignupForm $model
     * @param array $params
     */
    protected function _assign($model, $params)
    {
        $code = $params['code'];
        $isInvite = $model->getIsInviteRegister();
        $isNormal = $model->getIsNormalRegister();
        
        if (empty($model->getRegisterSwitch()) || (!$isInvite && !$isNormal)) {
            $this->error(Yii::t('wocenter/app', 'Thank you for your support, the system has now suspended the registration of new users.'));
        }
        
        // 宽松模式
        if ($model->getInviteRule() == 0) {
            // 不论是否开通【邀请注册】，只要注册页面存在有效邀请码，则会按照此邀请码相关信息进行注册
            if (!empty($code)) {
                $InviteModel = new Invite();
                $data = $InviteModel->checkInviteCode($code);
                // 只开启邀请注册，则验证邀请码合法性，否则只要存在有效邀请码，均显示该邀请码相关信息
                if ($isInvite && !$isNormal && $data === false) {
                    $this->error($InviteModel->message, ['/passport/common/invite-signup']);
                } elseif ($data !== false) {
                    // 获取注册身份列表
                    $this->assign('registerIdentityList', $model->getRegisterIdentityList($data['invite_type']));
                    $isInvite = false;
                } // 邀请码不合法则不显示相关信息
                else {
                }
            } // 只开启邀请注册且不存在邀请码，则跳转到邀请注册页面
            elseif ($isInvite && !$isNormal) {
                $this->error(Yii::t('wocenter/app', 'System currently supports only invited to register.'),
                    ['/passport/common/invite-signup']
                );
            }
        } // 严谨模式
        else {
            // 只有开通【邀请注册】且存在邀请码才显示该邀请码相关信息
            if ($isInvite && !empty($code)) {
                $InviteModel = new Invite();
                $data = $InviteModel->checkInviteCode($code);
                if ($data === false) {
                    $this->error($InviteModel->message, ['/passport/common/invite-signup']);
                }
                // 获取注册身份列表
                $this->assign('registerIdentityList', $model->getRegisterIdentityList($data['invite_type']));
                $isInvite = false;
            } // 只开启邀请注册且不存在邀请码，则跳转到邀请注册页面
            elseif (!$isNormal) {
                $this->error(Yii::t('wocenter/app', 'System currently supports only invited to register.'),
                    ['/passport/common/invite-signup']
                );
            }
        }
        
        $this->assign([
            'model' => $model,
            'registerTypeTextTabList' => $model->getOpenRegisterTypeTextList(), // 只显示系统开放的注册方式
            'showInviteRegisterType' => $isInvite, // 是否开启邀请注册
            'isOpenEmailVerify' => $model->isOpenEmailValidate(), // 是否开启邮箱验证
            'isOpenMobileVerify' => $model->isOpenMobileValidate(), // 是否开启手机验证
        ]);
    }
    
    protected function afterSignup()
    {
        /**
         * 邮箱验证类型
         * - 0:不验证
         * - 1:注册前发送验证邮件
         * - 2:注册后发送激活邮件
         */
        $url = '';
        $message = '';
        switch (Wc::$service->getSystem()->getConfig()->get('EMAIL_VERIFY_TYPE')) {
            case 0:
            case 1:
                $message = '注册成功，正在为您跳转至登录页面～';
                $url = Url::to(Yii::$app->getUser()->loginUrl);
                break;
            case 2:
                $message = '注册成功，激活邮件已经发出，激活成功后即可登陆您的账户~';
                break;
        }
        
        return ['url' => $url, 'message' => $message];
    }
    
}