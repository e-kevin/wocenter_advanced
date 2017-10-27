<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\core\Model;
use wocenter\libs\Utils;
use wocenter\Wc;
use wocenter\helpers\SecurityHelper;
use wocenter\helpers\StringHelper;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\traits\LoadModelTrait;
use Yii;
use yii\helpers\Url;
use yii\web\Cookie;

/**
 * 账户安全表单模型
 */
class SecurityForm extends Model
{
    
    use LoadModelTrait;
    
    const SCENARIO_FIND_PASSWORD = 'find-password';
    const SCENARIO_RESET_PASSWORD = 'reset-password';
    const SCENARIO_ACTIVE_ACCOUNT = 'activate-account';
    const SCENARIO_CHANGE_PASSWORD = 'change-password';
    
    public $email;
    public $captcha;
    public $oldPassword;
    public $password;
    public $passwordRepeat;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $showVerify = Utils::showVerify($this->getScenario());
        
        return [
            // 通用场景
            ['email', 'trim'],
            ['captcha', 'captcha',
                'when' => function () use ($showVerify) {
                    return $showVerify;
                },
                'whenClient' => "function (attribute, value) {return {$showVerify};}",
                'captchaAction' => Yii::$app->params['captchaAction'],
            ],
            // 找回密码场景|重新发送激活邮件
            ['email', 'required', 'on' => [self::SCENARIO_FIND_PASSWORD, self::SCENARIO_ACTIVE_ACCOUNT]],
            ['email', 'email', 'on' => [self::SCENARIO_FIND_PASSWORD, self::SCENARIO_ACTIVE_ACCOUNT]],
            ['email', 'string', 'length' => [PassportForm::EMAIL_LENGTH_MIN, PassportForm::EMAIL_LENGTH_MAX],
                'on' => [self::SCENARIO_FIND_PASSWORD, self::SCENARIO_ACTIVE_ACCOUNT]],
            // 重置密码|更改密码
            [['oldPassword', 'password', 'passwordRepeat'], 'required',
                'on' => [self::SCENARIO_RESET_PASSWORD, self::SCENARIO_CHANGE_PASSWORD]],
            ['password', 'string', 'length' => [PassportForm::PWD_LENGTH_MIN, PassportForm::PWD_LENGTH_MAX],
                'on' => [self::SCENARIO_RESET_PASSWORD, self::SCENARIO_CHANGE_PASSWORD]],
            ['password', 'compare', 'compareAttribute' => 'oldPassword',
                'message' => Yii::t('wocenter/app', 'The new password cannot be the same as the original password.'),
                'operator' => '!==',
                'on' => [self::SCENARIO_RESET_PASSWORD, self::SCENARIO_CHANGE_PASSWORD]],
            ['passwordRepeat', 'compare', 'compareAttribute' => 'password',
                'message' => Yii::t('wocenter/app', 'Please confirm your password again.'),
                'on' => [self::SCENARIO_RESET_PASSWORD, self::SCENARIO_CHANGE_PASSWORD]],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_FIND_PASSWORD] = ['email', 'captcha'];
        $scenarios[self::SCENARIO_RESET_PASSWORD] = ['password', 'passwordRepeat', 'captcha'];
        $scenarios[self::SCENARIO_CHANGE_PASSWORD] = ['oldPassword', 'password', 'passwordRepeat', 'captcha'];
        $scenarios[self::SCENARIO_ACTIVE_ACCOUNT] = ['email', 'captcha'];
        
        return $scenarios;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('wocenter/app', 'Email'),
            'captcha' => Yii::t('wocenter/app', 'Captcha'),
            'oldPassword' => Yii::t('wocenter/app', 'Original Password'),
            'password' => Yii::t('wocenter/app', 'Password'),
            'passwordRepeat' => Yii::t('wocenter/app', 'RePassword'),
        ];
    }
    
    /**
     * 找回密码
     *
     * @return array 操作失败
     * @return boolean true - 操作成功 false - 操作失败
     */
    public function findPassword()
    {
        if ($this->validate()) {
            /** @var BaseUser $user */
            $user = BaseUser::findByIdentity($this->email);
            if (false == $user) {
                $this->message = '该邮箱未注册，请确认您注册时使用的邮箱';
                
                return false;
            }
            $actionService = Wc::$service->getAction();
            if ($actionService->checkLimit('find_password', BaseUser::tableName(), $user->id)) {
                $user->generatePasswordResetToken();
                if ($user->save(false)) {
                    Yii::$app->getResponse()->getCookies()->add(new Cookie([
                        'name' => '_findPwByEmail',
                        'value' => $this->email,
                        'expire' => time() + Wc::$service->getSystem()->getConfig()->get('PASSWORD_RESET_TOKEN_EXPIRE'),
                    ]));
                    
                    // 发送邮件通知
                    Wc::$service->getNotification()->sendNotify('password_reset', $this->email, [
                        'reset_url' => Url::toRoute(['/reset-password', 'token' => $user->password_reset_token], true),
                    ]);
                    Wc::$service->getLog()->create('find_password', BaseUser::tableName(), $user->id, $user->id);
                    
                    return true;
                } else {
                    $this->message = $user->message;
                    
                    return false;
                }
            } else {
                $this->message = $actionService->getInfo();
            }
        }
        
        return false;
    }
    
    /**
     * 重置密码
     *
     * todo 验证令牌合法性
     *
     * @param string $token 重置令牌
     *
     * @return boolean
     */
    public function resetPasswordByToken($token)
    {
        $User = BaseUser::findByPasswordResetToken($token);
        if (is_null($User)) {
            $this->message = '重置密码链接已失效，请重新找回';
            
            return false;
        }
        if ($this->validate()) {
            $User->password = $this->password;
            $User->removePasswordResetToken();
            if ($User->save(false)) {
                Yii::$app->getResponse()->getCookies()->remove('_findPwByEmail');
                
                // 发送邮件通知
                Wc::$service->getNotification()->sendNotify('password_reset_ok', $User->email, [
                    'new_password' => SecurityHelper::markString($this->password), // 加密部分字母
                ]);
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 激活用户
     * 1） 根据激活码激活用户
     *
     * @param string $code
     *
     * @return boolean true - 激活成功 false - 激活失败
     */
    public function activateUser($code)
    {
        $data = $this->parseActivationCode($code);
        if (empty($data)) {
            $this->message = '抱歉，激活链接已失效，请重新发送激活邮件';
            
            return false;
        }
        /** @var BaseUser $UserInfo */
        $UserInfo = BaseUser::find()->select('id,is_active,validate_email')->where([
            'email' => $data[2],
            'id' => $data[0],
        ])->one();
        if (empty($UserInfo)) {
            $this->message = '抱歉，激活链接已失效，请重新发送激活邮件';
            
            return false;
        }
        
        if (0 == $UserInfo->validate_email) {
            $UserInfo->validate_email = 1;
        }
        $UserInfo->is_active = 1;
        
        return $UserInfo->save(false);
    }
    
    /**
     * 激活账户
     * 1) 发送激活邮件，发送激活码
     *
     * @return boolean true - 发送成功 false - 发送失败
     */
    public function activateAccount()
    {
        if ($this->validate()) {
            $user = BaseUser::find()->select('id,email,created_at,validate_email')
                ->where(['email' => $this->email]
                )->asArray()->one();
            if (empty($user)) {
                $this->message = '找不到该邮箱注册信息';
                
                return false;
            }
            $actionService = Wc::$service->getAction();
            if ($actionService->checkLimit('send_active_email', BaseUser::tableName(), $user['id'])) {
                Wc::$service->getLog()->create('send_active_email', BaseUser::tableName(), $user['id'], $user['id']);
                Wc::$service->getNotification()->sendNotify('register_active', $user['email'], [
                    'app_name' => Yii::$app->params['app.name'],
                    'active_url' => Url::toRoute(['/activate-user', 'code' => $this->generateActivationCode($user)], true),
                ]);
                
                return true;
            } else {
                $this->message = $actionService->getInfo();
            }
        }
        
        return false;
    }
    
    /**
     * 生成激活码，默认有效期30分钟
     *
     * @param array $user_info 用户的相关信息 [id, email, created_at]
     * @param string $param 额外参数  格式为：name:value
     * @param string $rand 随机码
     * @param integer $expire 激活码有效期，默认30分钟
     *
     * @return string 激活码
     */
    public function generateActivationCode($user_info, $param = '', $rand = '', $expire = 1800)
    {
        $code[] = $user_info['id'];
        $code[] = empty($rand) ? StringHelper::randString(11) : $rand;
        $code[] = $user_info['email'];
        $code[] = $user_info['created_at'];
        $code[] = $param;
        
        return SecurityHelper::encrypt(implode('/', $code), '', $expire);
    }
    
    /**
     * 解析激活码
     *
     * @param string $code 激活码
     *
     * @return null 激活码无效
     * @return array [id, rand, email, created_at, param]
     */
    protected function parseActivationCode($code)
    {
        $data = SecurityHelper::decrypt($code);
        if ($data) {
            $data = explode('/', $data);
        }
        
        return $data;
    }
    
    /**
     * 初始化用户密码，默认为 123456
     *
     * @param string|integer $identity 验证标识 username、email、mobile
     *
     * @return boolean true - 重置成功 false - 重置失败
     */
    public function initPassword($identity)
    {
        if (is_array($identity)) {
            $this->message = Yii::t('wocenter/app', 'Only one user can be operated at a time.');
            
            return false;
        }
        if (Yii::$app->getUser()->id == $identity) {
            $this->message = Yii::t('wocenter/app', "Can't do yourself.");
            
            return false;
        }
        $ucenterService = Wc::$service->getPassport()->getUcenter();
        if ($ucenterService->initPassword($identity)) {
            $this->message = $ucenterService->getInfo();
            
            return true;
        } else {
            $this->message = $ucenterService->getInfo();
            
            return false;
        }
    }
    
    /**
     * 解锁用户
     *
     * @param integer $uid 待解锁的用户ID
     *
     * @return boolean true - 解锁成功|false - 解锁失败
     */
    public function unlockUser($uid)
    {
        /** @var BaseUser $user */
        $user = $this->loadModel(BaseUser::className(), $uid, false);
        if ($user == null) {
            $this->message = '解锁失败，用户不存在';
            
            return false;
        }
        $user->status = BaseUser::STATUS_ACTIVE;
        if ($user->save(false)) {
            Wc::$service->getLog()->delete('error_password', BaseUser::tableName(), $uid, $uid);
            
            return true;
        } else {
            $this->message = '解锁失败，Ucenter用户中心未知错误';
            
            return false;
        }
    }
    
    /**
     * 更改当前登录用户密码
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }
        $ucenterService = Wc::$service->getPassport()->getUcenter();
        if ($ucenterService->verifyCurrentUser($this->oldPassword)) {
            /** @var BaseUser $user */
            $user = $ucenterService->getData();
            $user->password = $this->password;
            if ($user->save(false)) {
                return true;
            } else {
                $this->message = $user->message;
                
                return false;
            }
        } else {
            $this->message = $ucenterService->getInfo();
            
            return false;
        }
    }
    
}
