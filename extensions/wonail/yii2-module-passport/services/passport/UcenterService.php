<?php

namespace wocenter\backend\modules\passport\services\passport;

use wocenter\backend\modules\passport\services\PassportService;
use wocenter\core\Service;
use wocenter\libs\Utils;
use wocenter\backend\modules\account\models\UserIdentity;
use wocenter\backend\modules\account\models\UserProfile;
use wocenter\backend\modules\passport\models\LoginForm;
use wocenter\backend\modules\passport\models\SecurityForm;
use wocenter\backend\modules\passport\models\SignupForm;
use wocenter\backend\modules\passport\services\passport\events\updateLoginLog;
use wocenter\Wc;
use wocenter\helpers\StringHelper;
use wocenter\backend\modules\account\models\BaseUser;
use Yii;
use yii\base\InvalidValueException;
use yii\helpers\Url;
use wocenter\interfaces\IdentityInterface;
use yii\web\NotFoundHttpException;
use yii\web\User;

/**
 * 认证中心服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class UcenterService extends Service
{
    
    /**
     * @var PassportService 父级服务类
     */
    public $service;
    
    /**
     * @var BaseUser 已经认证的用户对象
     */
    private $_user;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'ucenter';
    }
    
    /**
     * 登陆用户
     *
     * @param string $identity $identity 验证标识 username、email、mobile
     * @param string $password 密码
     * @param integer $rememberMe 记住我，默认不记住
     *
     * @return boolean|array
     *  - true: 登陆成功
     *  - false: 登陆失败
     */
    public function login($identity, $password, $rememberMe = 0)
    {
        // 验证用户
        if ($this->verifyUser($identity, $password)) {
            // 没有注册流程或已完成注册流程，则直接登录，否则执行注册流程
            if (empty(Wc::$service->getSystem()->getConfig()->kanban('REGISTER_STEP')) ||
                !(new UserIdentity())->needStep($this->_data->id, $identity, $rememberMe)
            ) {
                return $this->quickLogin($this->_data, $rememberMe);
            } else {
                // 执行注册流程
                $this->_info = LoginForm::NEED_STEP;
                $this->_status = false;
                
                return $this->_status;
            }
        } else {
            return $this->_status;
        }
    }
    
    /**
     * 无密码用户快速登录认证
     *
     * @param string|BaseUser $identity 验证标识 username、email、mobile
     * @param integer $rememberMe 记住我，默认不记住
     *
     * @return boolean true：登录成功 false - 登录失败
     */
    public function quickLogin($identity, $rememberMe = 0)
    {
        if (!$identity instanceof IdentityInterface) {
            $identity = $this->getUser($identity);
        }
        
        // todo 增加对不同系统的验证，如日后的Account账户系统不论用户状态如何均可登录，前台和后台则受配置约束
        // 关闭登录许可则验证用户是否已经激活
        if (Wc::$service->getSystem()->getConfig()->get('NEED_ACTIVE') == 0 && !$identity->is_active) {
            $this->_info = Yii::t('wocenter/app', 'Please activate your account.');
            
            return false;
        }
        
        Yii::$app->getUser()->on(User::EVENT_AFTER_LOGIN, [new updateLoginLog(), 'run']);
        
        return Yii::$app->getUser()->login($identity, $rememberMe);
    }
    
    /**
     * 注册用户
     *
     * @param string $username 用户名
     * @param string $password 用户密码
     * @param string $email 用户邮箱
     * @param string $mobile 用户手机号码
     * @param boolean $autoGenerate 是否为快速生成用户操作，默认为false，为true时将禁用行为限制检测和邮件发送功能
     * @param integer $createdBy 注册方式，默认为用户注册
     *
     * @return boolean `true`: 注册成功，通过[[getData()]]方法返回用户数据数组[id, username, email, mobile, created_at]
     * @throws NotFoundHttpException
     */
    public function signup($username, $password, $email = null, $mobile = null, $autoGenerate = false, $createdBy = BaseUser::CREATED_BY_USER)
    {
        $class = Yii::$app->getUser()->identityClass;
        /* @var $model BaseUser */
        $model = new $class();
        $actionService = Wc::$service->getAction();
        if (!$autoGenerate && !$actionService->checkLimit('register', $model::tableName())) {
            $this->_info = $actionService->getInfo();
            
            return $this->_status;
        }
        
        $model->username = $username ?: $this->_randUsername(); // 如果username为空，则生成默认用户名
        $model->password = $password;
        $model->email = $email;
        $model->mobile = $mobile;
        
        if (!$autoGenerate) {
            // 已经通过验证则自动激活账户
            $registerSwitch = explode(',', Wc::$service->getSystem()->getConfig()->get('REGISTER_SWITCH'));
            /**
             * 如果开启邮箱验证并且是邮箱注册，则注册成功后标记邮箱已验证和已激活
             * 邮箱验证类型
             * - 0:不验证
             * - 1:注册前发送验证邮件
             * - 2:注册后发送激活邮件
             */
            if (Wc::$service->getSystem()->getConfig()->get('EMAIL_VERIFY_TYPE') == 1
                && in_array(SignupForm::REGISTER_TYPE_BY_EMAIL, $registerSwitch)
                && !empty($model->email)
            ) {
                $model->validate_email = 1;
                $model->is_active = 1;
            }
            /**
             * 如果开启手机验证并且是手机注册，则注册成功后标记手机已验证和已激活
             * 手机验证类型
             * - 0:不验证
             * - 1:注册前验证手机
             */
            if (Wc::$service->getSystem()->getConfig()->get('MOBILE_VERIFY_TYPE') == 1
                && in_array(SignupForm::REGISTER_TYPE_BY_MOBILE, $registerSwitch)
                && !empty($model->mobile)
            ) {
                $model->validate_mobile = 1;
                $model->is_active = 1;
            }
            $model->created_by = $createdBy;
        } else {
            $model->created_by = $model::CREATED_BY_SYSTEM;
        }
        
        $this->_status = Wc::transaction(function () use ($model, $autoGenerate) {
            // 注册成功
            if ($model->save(false)) {
                $this->afterSignup($model->attributes, $autoGenerate);
                $this->_status = true;
                $this->_data = [
                    $model->id,
                    $model->username,
                    $model->email,
                    $model->mobile,
                    $model->created_at,
                ];
                
                return $this->_status;
            } // 注册失败
            else {
                $this->_info = Yii::t('wocenter/app', 'Signup failure.');
                
                return false;
            }
        });
        
        return $this->_status;
    }
    
    /**
     * 注册成功后执行
     *
     * @param array $attributes 注册成功后的用户信息数组
     * @param boolean $autoGenerate 是否为快速生成用户操作，默认为false，为true时将禁用行为限制检测和邮件发送功能
     */
    protected function afterSignup($attributes, $autoGenerate = false)
    {
        // 创建用户详情
        Yii::$app->getDb()->createCommand()->insert(UserProfile::tableName(), [
            'uid' => $attributes['id'],
            'nickname' => $attributes['username'],
            'reg_ip' => Utils::getClientIp(),
            'reg_time' => $attributes['created_at'],
            'status' => $attributes['status'],
        ])->execute();
        
        // 记录操作日志
        /* @var $class BaseUser */
        $class = Yii::$app->getUser()->identityClass;
        Wc::$service->getLog()->create('register', $class::tableName(), $attributes['id']);
        
        if (!$autoGenerate) {
            /**
             * 邮箱验证类型
             * - 0:不验证
             * - 1:注册前发送验证邮件
             * - 2:注册后发送激活邮件
             */
            $registerSwitch = explode(',', Wc::$service->getSystem()->getConfig()->get('REGISTER_SWITCH'));
            if (Wc::$service->getSystem()->getConfig()->get('EMAIL_VERIFY_TYPE') == 2
                && in_array(SignupForm::REGISTER_TYPE_BY_EMAIL, $registerSwitch)
                && !empty($attributes['email'])
            ) {
                Wc::$service->getLog()->create('send_active_email', $class::tableName(), $attributes['id']);
                Wc::$service->getNotification()->sendNotify('register_active', $attributes['username'], [
                    'app_name' => Yii::$app->params['app.name'],
                    'active_url' => Url::toRoute([
                        '/activate-user',
                        'code' => (new SecurityForm())->generateActivationCode($attributes),
                    ], true),
                ]);
            }
            
            // todo 发送欢迎邮件
            if (Wc::$service->getSystem()->getConfig()->get('SEND_REGISTER_WELCOME')) {
//                Wc::$service->getNotification()->sendNotify('register_welcome', $attributes['username']);
            }
        }
        
        // todo 添加默认邀请码
    }
    
    /**
     * 退出登录
     */
    public function logout()
    {
        return Yii::$app->getUser()->logout();
    }
    
    /**
     * 初始化用户密码，默认为 123456
     *
     * @param integer $identity 验证标识 id
     *
     * @return boolean
     */
    public function initPassword($identity)
    {
        /** @var BaseUser $class */
        $class = Yii::$app->getUser()->identityClass;
        $userModel = $class::findOne($identity);
        if ($userModel == null) {
            $this->_info = Yii::t('wocenter/app', 'BaseUser does not exist.');
            
            return $this->_status;
        }
        $actionService = Wc::$service->getAction();
        if ($actionService->checkLimit('init_password', $class::tableName(), 1, $userModel->id)) {
            $userModel->password = 123456;
            if ($userModel->save(false)) {
                // todo 发送系统通知
                Wc::$service->getLog()->create('init_password', $class::tableName(), $userModel->id, 1);
                $this->_info = $actionService->getInfo();
                $this->_status = true;
            } else {
                $this->_info = Yii::t('wocenter/app', 'Password reset failed, please try again.');
            }
        } else {
            $this->_info = $actionService->getInfo();
        }
        
        return $this->_status;
    }
    
    /**
     * 验证用户状态信息
     *
     * @param string|integer $identity 验证标识 [username,email,mobile]
     * @param string $password 密码
     *
     * @return boolean
     * `true`: 验证成功，[[$this->getData()]]可获取用户详情对象
     * `false`: 验证失败，[[$this->getInfo()]]可获取错误信息提示
     * @throws NotFoundHttpException
     */
    public function verifyUser($identity, $password)
    {
        // 获取用户数据
        $user = $this->getUser($identity);
        
        switch ($user->status) {
            case $user::STATUS_ACTIVE:
                $this->verifyCurrentUser($password, $user);
                break;
            case $user::STATUS_LOCKED:
                $actionService = Wc::$service->getAction();
                // 锁定时间过期，则自动解锁
                if ($actionService->checkLimit('lock_user', $user->tableName(), $user->id)) {
                    // 系统级别自动解锁
                    $user->status = $user::STATUS_ACTIVE;
                    // 解锁成功则验证用户密码
                    if ($user->save(false, ['status'])) {
                        // 解锁成功后删除密码验证失败记录
                        Wc::$service->getLog()->delete('error_password', $user->tableName(), $user->id, $user->id);
                        $this->verifyCurrentUser($password, $user);
                    } else {
                        $this->_info = Yii::t('wocenter/app', 'Unknown error.');
                    }
                } else {
                    $this->_info = $actionService->getInfo();
                }
                break;
            case $user::STATUS_FORBIDDEN:
                $this->_info = Yii::t('wocenter/app', 'BaseUser is disabled.');
                break;
            default:
                $this->_info = Yii::t('wocenter/app', 'BaseUser status exception.');
                break;
        }
        
        return $this->_status;
    }
    
    /**
     * 验证指定用户（默认为当前登录用户）密码是否正确，用于确认用户身份
     *
     * @param string $password 用户密码
     * @param BaseUser|null $user 用户对象
     *
     * @return boolean
     * `true`: 验证成功，[[$this->getData()]]可获取用户详情对象
     * `false`: 验证失败，[[$this->getInfo()]]可获取错误信息提示
     * @throws NotFoundHttpException
     */
    public function verifyCurrentUser($password, BaseUser $user = null)
    {
        // 默认为当前登录用户
        if ($user === null) {
            $user = Yii::$app->getUser()->getIdentity();
        }
        if ($user === null) {
            $this->_info = Yii::t('wocenter/app', 'BaseUser does not exist.');
            
            return $this->_status;
        }
        
        // 密码验证成功
        if (Wc::$service->getPassport()->getValidation()->validatePassword($password, $user->password_hash)) {
            Wc::$service->getLog()->create('validate_password', $user->tableName(), $user->id, $user->id);
            $this->_status = true;
            $this->_data = $user;
        } // 密码验证失败
        else {
            $actionService = Wc::$service->getAction();
            if ($actionService->checkLimit('error_password', $user->tableName(), $user->id, $user->id)) {
                Wc::$service->getLog()->create('error_password', $user->tableName(), $user->id, $user->id);
            }
            
            $this->_info = $actionService->getInfo();
        }
        
        return $this->_status;
    }
    
    /**
     * 生成随机用户
     *
     * @return boolean
     */
    public function addRandUser()
    {
        return $this->signup($this->_randUsername(), StringHelper::randString(10), $this->_randEmail(), null, true);
    }
    
    /**
     * 生成随机邮箱地址
     *
     * @return string
     */
    private function _randEmail()
    {
        /** @var IdentityInterface $class */
        $class = Yii::$app->getUser()->identityClass;
        $email = StringHelper::randString(10) . '@wocenter.com';
        if ($class::findByIdentity($email)) {
            return $this->_randEmail();
        } else {
            return $email;
        }
    }
    
    /**
     * 生成随机用户名
     *
     * @return string
     */
    private function _randUsername()
    {
        /** @var IdentityInterface $class */
        $class = Yii::$app->getUser()->identityClass;
        $username = StringHelper::randString(10);
        if ($class::findByIdentity($username)) {
            return $this->_randUsername();
        } else {
            return $username;
        }
    }
    
    /**
     * 根据验证标识 username、email、mobile 查找用户，该方法同时验证该用户模型是否合法
     * 如果需要自定义该用户模型类，可配置Yii::$app->getUser()->identityClass属性
     *
     * @param string|integer $identity 验证标识 [username,email,mobile]
     *
     * @return IdentityInterface|BaseUser
     * @throws InvalidValueException
     * @throws NotFoundHttpException
     * @see yii\web\BaseUser::identityClass
     */
    public function getUser($identity)
    {
        if ($this->_user === null) {
            /** @var IdentityInterface $class */
            $class = Yii::$app->getUser()->identityClass;
            $this->_user = $class::findByIdentity($identity);
            if ($this->_user !== null) {
                if (!$this->_user instanceof IdentityInterface) {
                    throw new InvalidValueException("$class::findByIdentity() must return an object implementing `\\wocenter\\interfaces\\IdentityInterface`.");
                }
            } else {
                throw new NotFoundHttpException(Yii::t('wocenter/app', 'BaseUser does not exist.'));
            }
        }
        
        return $this->_user;
    }
    
    /**
     * 解析用户标识，返回标识类型
     *
     * @param string|integer $identity 用户标识 [手机,邮箱,用户名,用户ID]
     *
     * @return string mobile|email|id|username
     */
    public function parseIdentity($identity)
    {
        $validation = Wc::$service->getPassport()->getValidation();
        if ($validation->validateMobileFormat($identity)) {
            $param = 'mobile';
        } elseif ($validation->validateEmailFormat($identity)) {
            $param = 'email';
        } elseif (is_numeric($identity)) {
            $param = 'id';
        } else {
            $param = 'username';
        }
        
        return $param;
    }
    
}
