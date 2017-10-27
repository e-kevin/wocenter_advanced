<?php

namespace wocenter\frontend\modules\passport;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'frontend';
    
    /**
     * @inheritdoc
     */
    public $id = 'passport';
    
    /**
     * @inheritdoc
     */
    public $name = '通行证管理';
    
    /**
     * @inheritdoc
     */
    public $description = '提供登录、注册、密码找回、验证码等与账户安全相关的服务';
    
    /**
     * @inheritdoc
     */
    public $isSystem = false;
    
    /**
     * @inheritdoc
     */
    public function getUrlRules()
    {
        return [
            'login' => "{$this->id}/common/login",
            'logout' => "{$this->id}/common/logout",
            'logout-on-step' => "{$this->id}/common/logout-on-step",
            'signup' => "{$this->id}/common/signup",
            'step' => "{$this->id}/common/step",
            'invite-signup' => "{$this->id}/common/invite-signup",
            'find-password' => "{$this->id}/security/find-password",
            'find-password-successful' => "{$this->id}/security/find-password-successful",
            'reset-password' => "{$this->id}/security/reset-password",
            'activate-account' => "{$this->id}/security/activate-account",
            'activate-user' => "{$this->id}/security/activate-user",
            'change-password' => "{$this->id}/security/change-password",
        ];
    }
    
}
