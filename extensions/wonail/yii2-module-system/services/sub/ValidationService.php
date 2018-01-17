<?php

namespace wocenter\backend\modules\system\services\sub;

use wocenter\{
    backend\modules\system\services\SystemService, core\Service, helpers\ArrayHelper
};
use Yii;
use yii\validators\EmailValidator;

/**
 * 规则验证服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ValidationService extends Service
{
    
    /**
     * @var SystemService 父级服务类
     */
    public $service;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'validation';
    }
    
    /**
     * 验证邮件地址后缀的正确性
     *
     * @param string $email 邮箱
     *
     * @return boolean true - 后缀通过 false - 后缀不通过
     */
    public function validateEmailSuffix($email)
    {
        $matches = [];
        preg_match('/\b@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email, $matches);
        $email_suffix = $this->service->getConfig()->get('EMAIL_SUFFIX');   // 格式：@qq.com,@163.com
        if ($email_suffix && in_array($matches['0'], explode(',', $email_suffix))) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 检测邮箱是否被禁止使用
     *
     * @param string $email 邮箱
     *
     * @return boolean true - 可以使用，false - 禁止使用
     */
    public function validateEmail($email)
    {
        return true;
    }
    
    /**
     * 检测用户名是否包含系统保留字段
     *
     * @param string $username 用户名
     *
     * @return boolean true - 包含，false - 不包含
     */
    public function validateSystemUsername($username)
    {
        return ArrayHelper::inArrayCase($username, $this->service->getConfig()->get('FILTER_NICKNAME')) ? false : true;
    }
    
    /**
     * 检测用户名是否被禁用
     *
     * @param string $username 用户名
     *
     * @return boolean true - 可以使用，false - 禁止使用
     */
    public function validateUsername($username)
    {
        return true;
    }
    
    /**
     * 检测手机是否被禁止使用
     *
     * @param string $mobile 手机
     *
     * @return boolean true - 可以使用，false - 禁止使用
     */
    public function validateMobile($mobile)
    {
        return true;
    }
    
    /**
     * 检测手机是否合法
     *
     * @param string $mobile 手机
     *
     * @return boolean true - 可以使用，false - 禁止使用
     */
    public function validateMobileFormat($mobile)
    {
        return preg_match('/^((13[0-9])|147|(15[0-35-9])|180|(18[2-9]))[0-9]{8}$/A', $mobile);
    }
    
    /**
     * 检测邮箱地址是否合法
     *
     * @param string $email 邮箱
     *
     * @return boolean true - 可以使用，false - 禁止使用
     */
    public function validateEmailFormat($email)
    {
        return (new EmailValidator())->validate($email);
    }
    
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @param string $passwordHash
     *
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password, $passwordHash)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $passwordHash);
    }
    
}
