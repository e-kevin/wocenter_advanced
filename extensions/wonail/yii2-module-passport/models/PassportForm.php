<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\core\Model;
use wocenter\libs\Utils;
use Yii;

/**
 * 通行证表单基础模型
 */
class PassportForm extends Model
{
    
    /**
     * @var string 登陆场景
     */
    const SCENARIO_LOGIN = 'login';
    
    /**
     * @var string 注册场景
     */
    const SCENARIO_SIGNUP = 'signup';
    
    /**
     * @var string 邀请注册场景
     */
    const SCENARIO_SIGNUP_BY_INVITE = 'invite-signup';
    
    /**
     * @var integer 密码最小长度
     */
    const PWD_LENGTH_MIN = 6;
    
    /**
     * @var integer 密码最大长度
     */
    const PWD_LENGTH_MAX = 12;
    
    /**
     * @var integer 用户名最小长度
     */
    const USERNAME_LENGTH_MIN = 4;
    
    /**
     * @var integer 用户名最大长度
     */
    const USERNAME_LENGTH_MAX = 16;
    
    /**
     * @var integer 邮箱最小长度
     */
    const EMAIL_LENGTH_MIN = 6;
    
    /**
     * @var integer 邮箱最大长度
     */
    const EMAIL_LENGTH_MAX = 32;
    
    /**
     * @var string 登陆验证标志
     */
    public $identity;
    
    /**
     * @var string 用户名
     */
    public $username;
    
    /**
     * @var string 手机
     */
    public $mobile;
    
    /**
     * @var string 邮箱
     */
    public $email;
    
    /**
     * @var string 密码
     */
    public $password;
    
    /**
     * @var string 重复密码
     */
    public $passwordRepeat;
    
    /**
     * @var string 验证码
     */
    public $captcha;
    
    /**
     * @var string 邮箱验证码
     */
    public $emailVerifyCode;
    
    /**
     * @var string 手机验证码
     */
    public $mobileVerifyCode;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $showVerify = Utils::showVerify();
        
        return [
            // 通用场景
            [['identity', 'username', 'email', 'mobile'], 'trim'],
            ['password', 'string', 'length' => [self::PWD_LENGTH_MIN, self::PWD_LENGTH_MAX]],
            ['captcha', 'captcha',
                'when' => function () use ($showVerify) {
                    return $showVerify;
                },
                'whenClient' => "function (attribute, value) {return {$showVerify};}",
                'captchaAction' => Yii::$app->params['captchaAction'],
            ],
            // [邮箱、手机]验证码
            [['emailVerifyCode', 'mobileVerifyCode'], 'required'],
            // 验证密码
            [['password', 'passwordRepeat'], 'required'],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password',
                'message' => Yii::t('wocenter/app', 'Please confirm your password again.'),
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'identity' => Yii::t('wocenter/app', 'Identity'),
            'username' => Yii::t('wocenter/app', 'Username'),
            'mobile' => Yii::t('wocenter/app', 'Mobile'),
            'email' => Yii::t('wocenter/app', 'Email'),
            'password' => Yii::t('wocenter/app', 'Password'),
            'passwordRepeat' => Yii::t('wocenter/app', 'RePassword'),
            'rememberMe' => Yii::t('wocenter/app', 'Remember Me'),
            'captcha' => Yii::t('wocenter/app', 'Captcha'),
            'emailVerifyCode' => Yii::t('wocenter/app', 'Email Code'),
            'mobileVerifyCode' => Yii::t('wocenter/app', 'Mobile Code'),
            'registerType' => Yii::t('wocenter/app', 'Register Type'),
            'code' => Yii::t('wocenter/app', 'Invite Code'),
            'registerIdentity' => Yii::t('wocenter/app', 'Register Identity'),
            'rank' => Yii::t('wocenter/app', 'Rank'),
            'step' => Yii::t('wocenter/app', 'Step'),
        ];
    }
    
}
