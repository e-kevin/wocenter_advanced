<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\events\UserEvent;
use wocenter\interfaces\IdentityInterface;
use wocenter\Wc;
use Yii;

/**
 * 登陆表单模型
 */
class LoginForm extends PassportForm
{
    
    /**
     * @var string 登录前事件
     */
    const EVENT_BEFORE_LOGIN = 'before_login';
    
    /**
     * @var string 登录后事件
     */
    const EVENT_AFTER_LOGIN = 'after_login';
    
    /**
     * @var string 跳转至流程页面
     */
    const NEED_STEP = 'redirectStepPage';
    
    /**
     * @var string 步骤检测时登陆信息的临时保存，为执行步骤流程提供用户登陆相关信息
     * @see \wocenter\backend\modules\account\models\UserIdentity::needStep()
     */
    const TMP_LOGIN = 'tmp_login';
    
    /**
     * @var boolean 记住我
     */
    public $rememberMe = false;
    
    /**
     * @var integer 记住登录验证时长
     */
    public $rememberMeDuration;
    
    /**
     * TODO 系统安全配置【是否使用记住我功能】
     * @var boolean 是否使用记住我功能
     */
    protected $useRememberMe = false;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->rememberMeDuration = 3600 * 24 * 30; // 默认30天
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['identity', 'required', 'message' => Yii::t('wocenter/app', 'Empty identity')],
            ['password', 'required'],
            ['rememberMe', 'boolean'],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            parent::SCENARIO_DEFAULT => ['identity', 'password', 'rememberMe', 'captcha'],
        ];
    }
    
    /**
     * @return boolean 是否使用记住我功能
     */
    public function getUseRememberMe()
    {
        return $this->useRememberMe;
    }
    
    /**
     * 用户登录认证，包含注册流程检测
     *
     * @return boolean
     *  - false：登录失败，调用`$message`获取错误信息。如果`$message`信息为[[self::NEED_STEP]]则表示需要跳转至注册流程页面
     *  - true：登录成功
     */
    public function login()
    {
        if ($this->validate() == false) {
            return false;
        }
        
        $duration = $this->rememberMe ? $this->rememberMeDuration : 0;
        $ucenterService = Wc::$service->getPassport()->getUcenter();
        $identity = $ucenterService->getUser($this->identity);
        if ($this->beforeLogin($identity, false, $duration)) {
            if ($ucenterService->login($this->identity, $this->password, $duration)) {
                $this->afterLogin($identity, false, $duration);
                
                return true;
            } else {
                $this->message = $ucenterService->getInfo();
            }
        }
        
        return false;
    }
    
    /**
     * This method is called before logging in a user.
     * The default implementation will trigger the [[EVENT_BEFORE_LOGIN]] event.
     * If you override this method, make sure you call the parent implementation
     * so that the event is triggered.
     *
     * @param IdentityInterface $identity the user identity information
     * @param bool $cookieBased whether the login is cookie-based
     * @param int $duration number of seconds that the user can remain in logged-in status.
     * If 0, it means login till the user closes the browser or the session is manually destroyed.
     *
     * @return bool whether the user should continue to be logged in
     */
    protected function beforeLogin($identity, $cookieBased, $duration)
    {
        $event = new UserEvent([
            'identity' => $identity,
            'cookieBased' => $cookieBased,
            'duration' => $duration,
        ]);
        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);
        
        return $event->isValid;
    }
    
    /**
     * This method is called after the user is successfully logged in.
     * The default implementation will trigger the [[EVENT_AFTER_LOGIN]] event.
     * If you override this method, make sure you call the parent implementation
     * so that the event is triggered.
     *
     * @param IdentityInterface $identity the user identity information
     * @param bool $cookieBased whether the login is cookie-based
     * @param int $duration number of seconds that the user can remain in logged-in status.
     * If 0, it means login till the user closes the browser or the session is manually destroyed.
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        $this->trigger(self::EVENT_AFTER_LOGIN, new UserEvent([
            'identity' => $identity,
            'cookieBased' => $cookieBased,
            'duration' => $duration,
        ]));
    }
    
}
