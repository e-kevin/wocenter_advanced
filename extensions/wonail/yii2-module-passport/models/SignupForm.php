<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\backend\modules\account\models\BaseUser;
use wocenter\backend\modules\account\models\UserIdentity;
use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\modules\operate\models\InviteLog;
use wocenter\backend\modules\operate\models\InviteType;
use wocenter\Wc;
use Yii;

/**
 * 注册表单模型
 *
 * @property string $recommendRegisterType 推荐注册类型
 * @property array $registerTypeList 注册类型列表
 * @property array $registerTypeTextList 注册类型文本列表
 * @property boolean $isNormalRegister 是否普通注册方式，只读属性
 * @property boolean $isInviteRegister 是否邀请注册方式，只读属性
 * @property boolean $isAutoRegister 是否自动注册方式，只读属性
 * @property boolean $registerSwitch 注册开关
 * @property array $registerIdentityList 注册身份列表
 * @property integer $inviteRule 邀请制度
 */
class SignupForm extends PassportForm
{
    
    /**
     * @var integer 用户名注册类型
     */
    const REGISTER_TYPE_BY_USERNAME = 0;
    
    /**
     * @var integer 邮箱注册类型
     */
    const REGISTER_TYPE_BY_EMAIL = 1;
    
    /**
     * @var integer 手机注册类型
     */
    const REGISTER_TYPE_BY_MOBILE = 2;
    
    /**
     * @var integer 普通注册
     */
    const REGISTER_TYPE_NORMAL = 0;
    
    /**
     * @var integer 邀请注册
     */
    const REGISTER_TYPE_INVITE = 1;
    
    /**
     * @var integer 系统自动生成
     */
    const REGISTER_TYPE_AUTO = 2;
    
    /**
     * @var integer 第三方账号绑定
     */
    const REGISTER_TYPE_OTHER = 3;
    
    /**
     * @var string 邀请码
     */
    public $code;
    
    /**
     * @var integer 注册身份
     */
    public $registerIdentity;
    
    /**
     * @var integer 注册类型，0:用户名,1:邮箱,2:手机
     */
    public $registerType;
    
    /**
     * @var array|integer 注册开关，是否允许注册，为空时则暂停新用户注册，0:用户名,1:邮箱,2:手机
     */
    private $_registerSwitch;
    
    /**
     * @var array 数据库里的注册类型数组
     */
    private $_registerTypeInDb;
    
    /**
     * @var boolean 是否普通注册方式
     */
    private $_isNormalRegister;
    
    /**
     * @var boolean 是否邀请注册方式
     */
    private $_isInviteRegister;
    
    /**
     * @var integer 邀请制度
     */
    private $_inviteRule;
    
    /**
     * @var string|integer 邀请注册方式刚注册的身份名称，用于添加邀请记录信息。
     * 值为数字时，则为身份ID
     * 值为字符串时，则为注册身份名
     */
    private $_registerIdentityName;
    
    /**
     * @var Invite 邀请码详情
     */
    private $_codeInfo;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            // 邀请注册场景
            ['code', 'required', 'when' => function () {
                return (
                    // 邀请注册页面，必须输入邀请码
                    $this->getScenario() == parent::SCENARIO_SIGNUP_BY_INVITE
                    // 普通注册页面如果只开通【邀请注册】，则必须输入邀请码
                    || ($this->getIsInviteRegister() && !$this->getIsNormalRegister())
                );
            }, 'on' => [
                self::SCENARIO_SIGNUP_BY_INVITE,
                self::SCENARIO_SIGNUP,
            ]],
            // 通用场景
            ['code', 'validateCode', 'when' => function () {
                // 邀请注册页面，时刻验证邀请码合法性
                if ($this->getScenario() == parent::SCENARIO_SIGNUP_BY_INVITE) {
                    return true;
                }
                // 宽松模式
                if ($this->getInviteRule() == 0) {
                    return !empty($this->code);
                } // 严谨模式
                else {
                    return !empty($this->code) && $this->getIsInviteRegister();
                }
            }, 'on' => [
                self::SCENARIO_SIGNUP_BY_INVITE,
                self::SCENARIO_SIGNUP,
            ]],
            // 注册场景
            // 验证注册类型
            ['registerType', 'required', 'on' => parent::SCENARIO_SIGNUP],
            ['registerType', 'in', 'range' => [
                self::REGISTER_TYPE_BY_USERNAME,
                self::REGISTER_TYPE_BY_EMAIL,
                self::REGISTER_TYPE_BY_MOBILE,
            ], 'on' => parent::SCENARIO_SIGNUP],
            // 用户名注册
            ['username', 'required', 'on' => parent::SCENARIO_SIGNUP],
            ['username', 'string', 'length' => [parent::USERNAME_LENGTH_MIN, parent::USERNAME_LENGTH_MAX], 'on' => parent::SCENARIO_SIGNUP],
            ['username', 'validateSystemUsername', 'on' => parent::SCENARIO_SIGNUP],
            ['username', 'validateUserName', 'on' => parent::SCENARIO_SIGNUP],
            ['username', 'match', 'pattern' => '/^[A-Za-z]+\w+$/',
                'on' => parent::SCENARIO_SIGNUP,
                'message' => Yii::t(
                    'wocenter/app',
                    'The {attribute} must begin with a letter, and only in English, figures and underscores.',
                    ['attribute' => Yii::t('wocenter/app', 'Username')]
                ),
            ],
            ['username', 'unique', 'targetClass' => Yii::$app->getUser()->identityClass,
                'message' => Yii::t('wocenter/app', 'Username is exist.'),
                'on' => parent::SCENARIO_SIGNUP,
            ],
            // 邮箱注册
            ['email', 'required', 'on' => parent::SCENARIO_SIGNUP],
            ['email', 'email', 'on' => parent::SCENARIO_SIGNUP],
            ['email', 'string', 'length' => [parent::EMAIL_LENGTH_MIN, parent::EMAIL_LENGTH_MAX], 'on' => parent::SCENARIO_SIGNUP],
            ['email', 'validateEmailSuffix', 'on' => parent::SCENARIO_SIGNUP],
            ['email', 'validateEmail', 'on' => parent::SCENARIO_SIGNUP],
            ['email', 'unique', 'targetClass' => Yii::$app->getUser()->identityClass,
                'message' => Yii::t('wocenter/app', 'Email is exist.'),
                'on' => parent::SCENARIO_SIGNUP,
            ],
            // 手机注册
            ['mobile', 'required', 'on' => parent::SCENARIO_SIGNUP],
            ['mobile', 'match', 'pattern' => '/^((13[0-9])|147|(15[0-35-9])|180|(18[2-9]))[0-9]{8}$/A', 'on' => parent::SCENARIO_SIGNUP],
            ['mobile', 'unique', 'targetClass' => Yii::$app->getUser()->identityClass,
                'message' => Yii::t('wocenter/app', 'Mobile is exist.'),
                'on' => parent::SCENARIO_SIGNUP,
            ],
            // 验证码
            [['emailVerifyCode', 'mobileVerifyCode'], 'validateVerifyCode'],
            // 注册身份
            ['registerIdentity', 'integer', 'on' => parent::SCENARIO_SIGNUP],
            ['registerIdentity', 'required', 'when' => function () {
                // 邀请码有效则验证注册身份合法性
                if ($this->_codeInfo != false) {
                    // 获取注册身份列表
                    $registerIdentityList = $this->getRegisterIdentityList($this->_codeInfo->invite_type);
                    // 不存在注册身份则不验证注册身份必要性
                    if (empty($registerIdentityList)) {
                        return false;
                    } elseif (!empty($this->registerIdentity)) {
                        if (!array_key_exists($this->registerIdentity, $registerIdentityList)) {
                            $this->addError('registerIdentity', '注册身份无效');
                            
                            return false;
                        } else {
                            // 注册身份名
                            $this->_registerIdentityName = $registerIdentityList[$this->registerIdentity];
                        }
                    }
                    
                    return true;
                } // 邀请码无效则不验证注册身份合法性
                else {
                    return false;
                }
            }, 'on' => parent::SCENARIO_SIGNUP],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios[parent::SCENARIO_SIGNUP] = ['password', 'passwordRepeat', 'captcha', 'code', 'registerType', 'registerIdentity'];
        $scenarios[parent::SCENARIO_SIGNUP_BY_INVITE] = ['captcha', 'code'];
        
        return $this->dynamicScenarios($scenarios);
    }
    
    /**
     * 动态设置系统场景，仅设置注册场景
     *
     * @param array $scenarios 场景
     *
     * @return array
     */
    protected function dynamicScenarios($scenarios)
    {
        if ($this->getScenario() !== parent::SCENARIO_SIGNUP) {
            return $scenarios;
        }
        // 根据所获取的提交方式动态设置需要保存的属性
        if (Yii::$app->getRequest()->getIsPost()) {
            $this->_dynamicScenariosByPost($scenarios);
        } else {
            $this->_dynamicScenariosByGet($scenarios);
        }
        
        return $scenarios;
    }
    
    /**
     * GET请求动态设置场景字段
     *
     * @param $scenarios
     */
    private function _dynamicScenariosByGet(&$scenarios)
    {
        foreach ($this->getRegisterTypeList() as $k => $v) {
            if (in_array($k, $this->getRegisterSwitch())) {
                $scenarios[parent::SCENARIO_SIGNUP][] = $v;
            }
        }
        if ($this->isOpenEmailValidate()) {
            $scenarios[parent::SCENARIO_SIGNUP][] = 'emailVerifyCode';
        }
        if ($this->isOpenMobileValidate()) {
            $scenarios[parent::SCENARIO_SIGNUP][] = 'mobileVerifyCode';
        }
    }
    
    /**
     * POST请求动态设置场景字段
     *
     * @param $scenarios
     */
    private function _dynamicScenariosByPost(&$scenarios)
    {
        $registerType = Yii::$app->getRequest()->getBodyParam($this->formName())['registerType'];
        // 根据注册类型，动态验证所需字段
        switch (true) {
            case $registerType == self::REGISTER_TYPE_BY_USERNAME:
                break;
            case $registerType == self::REGISTER_TYPE_BY_EMAIL:
                /**
                 * 邮箱验证类型
                 * - 0:不验证
                 * - 1:注册前发送验证邮件
                 * - 2:注册后发送激活邮件
                 */
                if (Wc::$service->getSystem()->getConfig()->get('EMAIL_VERIFY_TYPE') == 1) {
                    $scenarios[parent::SCENARIO_SIGNUP][] = 'emailVerifyCode';
                }
                break;
            case $registerType == self::REGISTER_TYPE_BY_MOBILE:
                /**
                 * 手机验证类型
                 * - 0:不验证
                 * - 1:注册前验证手机
                 */
                if (Wc::$service->getSystem()->getConfig()->get('MOBILE_VERIFY_TYPE') == 1) {
                    $scenarios[parent::SCENARIO_SIGNUP][] = 'mobileVerifyCode';
                }
                break;
        }
        $scenarios[parent::SCENARIO_SIGNUP][] = $this->registerTypeList[$registerType];
    }
    
    /**
     * 是否开启邮箱验证
     *
     * @return bool
     */
    public function isOpenEmailValidate()
    {
        /**
         * 邮箱验证类型
         * - 0:不验证
         * - 1:注册前发送验证邮件
         * - 2:注册后发送激活邮件
         */
        return Wc::$service->getSystem()->getConfig()->get('EMAIL_VERIFY_TYPE') == 1
            && in_array(self::REGISTER_TYPE_BY_EMAIL, $this->getRegisterSwitch());
    }
    
    /**
     * 是否开启手机验证
     *
     * @return bool
     */
    public function isOpenMobileValidate()
    {
        /**
         * 手机验证类型
         * - 0:不验证
         * - 1:注册前验证手机
         */
        return Wc::$service->getSystem()->getConfig()->get('MOBILE_VERIFY_TYPE') == 1
            && in_array(self::REGISTER_TYPE_BY_MOBILE, $this->getRegisterSwitch());
    }
    
    /**
     * 注册开关，是否允许注册，为空时则暂停新用户注册，0:用户名,1:邮箱,2:手机
     *
     * @return array 已开通的注册方式
     */
    public function getRegisterSwitch()
    {
        if ($this->_registerSwitch === null) {
            $this->_registerSwitch = Wc::$service->getSystem()->getConfig()->get('REGISTER_SWITCH')
                ? explode(',', Wc::$service->getSystem()->getConfig()->get('REGISTER_SWITCH'))
                : [];
        }
        
        return $this->_registerSwitch;
    }
    
    /**
     * 只显示系统开放的注册方式
     *
     * @return array 允许注册的类型文本列表
     */
    public function getOpenRegisterTypeTextList()
    {
        $registerTypeTextTabList = [];
        foreach ($this->getRegisterTypeTextList() as $k => $v) {
            if (in_array($k, $this->getRegisterSwitch())) {
                $registerTypeTextTabList[$k] = $v;
            }
        }
        
        return $registerTypeTextTabList;
    }
    
    /**
     * @return array 注册类型列表
     */
    public function getRegisterTypeList()
    {
        return [
            self::REGISTER_TYPE_BY_USERNAME => 'username',
            self::REGISTER_TYPE_BY_EMAIL => 'email',
            self::REGISTER_TYPE_BY_MOBILE => 'mobile',
        ];
    }
    
    /**
     * @return array 注册类型文本列表
     */
    public function getRegisterTypeTextList()
    {
        return [
            self::REGISTER_TYPE_BY_USERNAME => '用户名',
            self::REGISTER_TYPE_BY_EMAIL => '邮箱',
            self::REGISTER_TYPE_BY_MOBILE => '手机',
        ];
    }
    
    /**
     * 获取默认推荐注册方式
     *
     * @return integer 推荐注册方式
     */
    public function getRecommendRegisterType()
    {
        // 默认的推荐注册方式
        $recommendRegisterType = self::REGISTER_TYPE_BY_EMAIL;
        // 系统默认推荐的注册方式系统配置里没有开放，则获取系统配置里的第一个注册方式
        if (!empty($this->registerSwitch) && !in_array($recommendRegisterType, $this->registerSwitch)) {
            $recommendRegisterType = $this->registerSwitch[0];
        }
        
        return $recommendRegisterType;
    }
    
    /**
     * 数据库里的注册类型数组
     *
     * @return array
     */
    protected function getRegisterTypeInDb()
    {
        if ($this->_registerTypeInDb === null) {
            $this->_registerTypeInDb = Wc::$service->getSystem()->getConfig()->get('REGISTER_TYPE')
                ? explode(',', Wc::$service->getSystem()->getConfig()->get('REGISTER_TYPE'))
                : [];
        }
        
        return $this->_registerTypeInDb;
    }
    
    /**
     * 是否邀请注册方式
     *
     * @return boolean
     */
    public function getIsInviteRegister()
    {
        if ($this->_isInviteRegister === null) {
            $this->_isInviteRegister = in_array(self::REGISTER_TYPE_INVITE, $this->getRegisterTypeInDb());
        }
        
        return $this->_isInviteRegister;
    }
    
    /**
     * 是否普通注册方式
     *
     * @return boolean
     */
    public function getIsNormalRegister()
    {
        if ($this->_isNormalRegister === null) {
            $this->_isNormalRegister = in_array(self::REGISTER_TYPE_NORMAL, $this->getRegisterTypeInDb());
        }
        
        return $this->_isNormalRegister;
    }
    
    /**
     * 是否自动注册方式
     *
     * @return boolean
     */
    public function getIsAutoRegister()
    {
        return in_array(self::REGISTER_TYPE_AUTO, $this->getRegisterTypeInDb());
    }
    
    /**
     * 邀请制度
     *
     * @return integer
     */
    public function getInviteRule()
    {
        if ($this->_inviteRule === null) {
            $this->_inviteRule = Wc::$service->getSystem()->getConfig()->get('INVITE_RULE');
        }
        
        return $this->_inviteRule;
    }
    
    /**
     * 检测用户名是不是被禁止注册
     *
     * @param $attribute
     */
    public function validateSystemUsername($attribute)
    {
        if (!Wc::$service->getPassport()->getValidation()->validateSystemUsername($this->$attribute)) {
            $this->addError($attribute, Yii::t('wocenter/app', 'Illegal user name, including system reserved field.'));
        }
    }
    
    /**
     * 检测用户名是否被禁用
     *
     * @param $attribute
     */
    public function validateUserName($attribute)
    {
        if (!Wc::$service->getPassport()->getValidation()->validateUserName($this->$attribute)) {
            $this->addError($attribute, Yii::t('wocenter/app', 'Invalid username.'));
        }
    }
    
    /**
     * 验证邮件地址后缀的正确性
     *
     * @param $attribute
     */
    public function validateEmailSuffix($attribute)
    {
        if (!Wc::$service->getPassport()->getValidation()->validateEmailSuffix($this->$attribute)) {
            $this->addError($attribute, Yii::t('wocenter/app', 'Invalid email suffix.'));
        }
    }
    
    /**
     * 检测邮箱是不是被禁止注册
     *
     * @param $attribute
     */
    public function validateEmail($attribute)
    {
        if (!Wc::$service->getPassport()->getValidation()->validateEmail($this->$attribute)) {
            $this->addError($attribute, Yii::t('wocenter/app', 'Invalid email.'));
        }
    }
    
    /**
     * 检测[邮箱|手机号]验证码是否可用，只在注册方式为[email, mobile]时生效(该判断已在$this->_dynamicScenariosByPost()场景里设置)
     *
     * @param string $attribute 验证码类型 [emailVerifyCode, mobileVerifyCode]
     */
    public function validateVerifyCode($attribute)
    {
        if (!$this->hasErrors()) {
            $verifyService = Wc::$service->getPassport()->getVerify();
            if ($verifyService->validate(
                    $this->registerType == self::REGISTER_TYPE_BY_EMAIL
                        ? $this->email
                        : $this->mobile,
                    $this->$attribute) == false
            ) {
                $this->addError($attribute, $verifyService->getInfo());
            }
        }
    }
    
    /**
     * 检测邀请码是否可用
     *
     * @param string $attribute
     *
     * @return boolean
     */
    public function validateCode($attribute)
    {
        $InviteModel = new Invite();
        $this->_codeInfo = $InviteModel->checkInviteCode($this->$attribute, true);
        if ($this->_codeInfo == false && (
                $this->getScenario() === parent::SCENARIO_SIGNUP_BY_INVITE
                || $this->getInviteRule() == 1
            )
        ) {
            $this->addError($attribute, $InviteModel->message);
        }
    }
    
    /**
     * 获取激活状态的注册身份列表
     *
     * @param integer $inviteType 邀请码类型ID
     *
     * @return array [id => title]|[]
     */
    public function getRegisterIdentityList($inviteType = 0)
    {
        if (empty($inviteType)) {
            return [];
        }
        // 获取邀请码类型所绑定的身份IDS
        $identityIds = (new InviteType())->getIdentityIds($inviteType);
        // 获取身份列表
        $identities = (new Identity())->getSelectList([
            'status' => 1,
            'id' => $identityIds,
        ]);
        
        return $identities;
    }
    
    /**
     * 检测是否可以注册
     *
     * @return boolean
     */
    protected function validateRegisterSwitch()
    {
        // 是否允许注册
        if (empty($this->getRegisterSwitch()) ||
            (!$this->getIsNormalRegister() && !$this->getIsInviteRegister())
        ) {
            $this->message = Yii::t('wocenter/app',
                'Thank you for your support, the system has now suspended the registration of new users.'
            );
            
            return false;
        } // 注册方式是否有效
        elseif (!in_array($this->registerType, $this->getRegisterSwitch())) {
            $this->message = Yii::t('wocenter/app', 'System has been suspended {type} for registration.', [
                'type' => $this->registerTypeTextList[$this->registerType],
            ]);
            
            return false;
        } // 只开启邀请注册且不存在邀请码
        elseif ($this->getIsInviteRegister() && !$this->getIsNormalRegister() && empty($this->code)) {
            $this->message = Yii::t('wocenter/app', 'The system currently supports only invited registrations.');
            
            return false;
        }
        
        return true;
    }
    
    /**
     * 注册用户
     *
     * @return boolean 注册成功|注册失败，属性`message`返回提示信息
     */
    public function signup()
    {
        if ($this->validateRegisterSwitch() == false || $this->validate() == false) {
            return false;
        }
        
        return Wc::transaction(function () {
            $ucenterService = Wc::$service->getPassport()->getUcenter();
            $createdBy = $this->_codeInfo != false ? BaseUser::CREATED_BY_INVITE : BaseUser::CREATED_BY_USER;
            // 注册成功
            if ($ucenterService->signup($this->username, $this->password, $this->email, $this->mobile, false, $createdBy)) {
                // 注册身份为空，则绑定系统默认身份
                if (empty($this->registerIdentity)) {
                    $this->registerIdentity = UserIdentity::DEFAULT_IDENTITY;
                }
                $uid = $ucenterService->data[0];
                // 邀请注册（存在有效邀请码），则初始化邀请码所绑定的相关信息
                if ($this->_codeInfo != false) {
                    // 添加邀请注册成功日志
                    (new InviteLog())->create($this->_codeInfo, $uid, is_null($this->_registerIdentityName)
                        ? $this->registerIdentity
                        : $this->_registerIdentityName);
                    // 初始化邀请用户信息
                    (new Invite())->initInviteUser($this->_codeInfo, $uid);
                }
                // 绑定用户-身份关联信息
                $userIdentityModel = (new UserIdentity());
                $res = $userIdentityModel->bindUserIdentity($uid, $this->registerIdentity);
                if (!$res) {
                    throw new \Exception($userIdentityModel->message);
                }
                // 注册方式为邮箱或手机号时则删除验证码
                $this->_deleteVerifyCode();
                
                return true;
            } // 注册失败
            else {
                $this->message = $ucenterService->getInfo();
                
                return false;
            }
        });
    }
    
    /**
     * 删除邮箱或手机号验证码
     */
    private function _deleteVerifyCode()
    {
        // 注册方式为邮箱或手机号时则删除验证码
        switch (true) {
            /**
             * 邮箱验证类型
             * - 0:不验证
             * - 1:注册前发送验证邮件
             * - 2:注册后发送激活邮件
             */
            case $this->registerType == self::REGISTER_TYPE_BY_EMAIL
                && Wc::$service->getSystem()->getConfig()->get('EMAIL_VERIFY_TYPE') == 1:
                Wc::$service->getPassport()->getVerify()->delete($this->email, $this->emailVerifyCode);
                break;
            /**
             * 手机验证类型
             * - 0:不验证
             * - 1:注册前验证手机
             */
            case $this->registerType == self::REGISTER_TYPE_BY_MOBILE
                && Wc::$service->getSystem()->getConfig()->get('MOBILE_VERIFY_TYPE') == 1:
                Wc::$service->getPassport()->getVerify()->delete($this->mobile, $this->mobileVerifyCode);
                break;
        }
    }
    
    /**
     * 快速生成随机用户
     *
     * @return boolean
     */
    public function generateUser()
    {
        if (!$this->getIsAutoRegister()) {
            $this->message = Yii::t('wocenter/app', 'System has suspended the rapid generation of user functions.');
            
            return false;
        }
        $ucenterService = Wc::$service->getPassport()->getUcenter();
        if ($ucenterService->addRandUser()) {
            return true;
        } else {
            $this->message = $ucenterService->getInfo();
            
            return false;
        }
    }
    
}
