<?php

namespace wocenter\backend\modules\passport\services\passport;

use wocenter\backend\modules\passport\services\PassportService;
use wocenter\core\Service;
use wocenter\Wc;
use wocenter\helpers\StringHelper;
use wocenter\backend\modules\system\models\Verify;
use Yii;

/**
 * 验证中心服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class VerifyService extends Service
{
    
    /**
     * @var PassportService 父级服务类
     */
    public $service;
    
    /**
     * @var string|array|callable|Verify 验证码模型类
     */
    public $verifyModel = '\wocenter\backend\modules\system\models\Verify';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'verify';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->verifyModel = Yii::createObject($this->verifyModel);
    }
    
    /**
     * 发送验证码
     *
     * @param string $identity 验证类型 [mobile, email]
     * @param boolean $isRegisterPage 是否在注册页面请求发送，默认为false
     *
     * @return boolean
     */
    public function send($identity, $isRegisterPage = false)
    {
        $type = Wc::$service->getPassport()->getUcenter()->parseIdentity($identity);
        switch ($type) {
            case 'mobile':
                $sendType = 'send_sms_verify';
                $this->_info = Yii::t('wocenter/app', 'The phone number has been registered.');
                break;
            case 'email':
                $sendType = 'send_email_verify';
                $this->_info = Yii::t('wocenter/app', 'The email has been registered.');
                break;
            default:
                $this->_info = Yii::t('wocenter/app', 'Validate failure.');
                
                return $this->_status;
        }
        // 如果是在注册页面触发，则触发者为`系统`
        if ($isRegisterPage) {
            // 行为触发者
            $actionUser = 1;
        } else {
            // 用户主动触发，如果不存在相应用户则不发送验证码
            $userInfo = Wc::$service->getAccount()->info($identity);
            if ($userInfo == null) {
                $this->_info = Yii::t('wocenter/app', 'User does not exist.');
                
                return $this->_status;
            }
            $actionUser = $userInfo[0];
        }
        
        $actionService = Wc::$service->getAction();
        if ($actionService->checkLimit($sendType, $this->verifyModel->tableName(), $actionUser) == false) {
            $this->_info = $actionService->getInfo();
            
            return $this->_status;
        }
        /** @var Verify $class */
        $class = $this->verifyModel;
        $typeList = $class::$typeList;
        if ($this->generate($identity, $typeList[$type])) {
            // 发送验证码至相应终端
            switch ($type) {
                case 'mobile':
                    Wc::$service->getNotification()->getSms()->send();
                    $this->_status = false;
                    $this->_info = '系统暂未开通发送手机短信通道';
                    break;
                case 'email':
                    Wc::$service->getNotification()->getEmail()->send('main-html', [
                        'title' => '验证邮件',
                        'content' => '请尽快使用该验证码：' . $this->verifyModel->code,
                    ], $identity, '请尽快使用该验证码');
                    $this->_status = true;
                    break;
            }
            // 记录日志
            Wc::$service->getLog()->create($sendType, $this->verifyModel->tableName(), $this->verifyModel->id, $actionUser);
            
            return $this->_status;
        } else {
            $this->_info = Yii::t('wocenter/app', 'Failed to generate verification code.');
            
            return $this->_status;
        }
    }
    
    /**
     * 生成验证码
     *
     * @param string $identity 验证类型 [mobile, email]
     * @param integer $type 验证码类型 0 - 邮箱 1 - 手机 默认为0
     *
     * @return boolean
     */
    protected function generate($identity, $type = Verify::EMAIL)
    {
        // 生成前先删除该用户此前的验证码
        $this->verifyModel->deleteAll(['identity' => $identity, 'type' => $type]);
        $this->verifyModel->load([
            'identity' => $identity,
            'type' => $type,
            'code' => StringHelper::randString(6, 1), // 验证码,
        ], '');
        
        return $this->verifyModel->save(false);
    }
    
    /**
     * 验证验证码
     *
     * todo: 添加验证时长
     *
     * @param string $identity 验证类型 [mobile, email]
     * @param string $code 验证码
     *
     * @return boolean
     */
    public function validate($identity, $code)
    {
        if ($this->verifyModel->findOne(['identity' => $identity, 'code' => $code]) == false) {
            $this->_info = Yii::t('wocenter/app', 'Verification code is error.');
        } else {
            $this->_status = true;
        }
        
        return $this->_status;
    }
    
    /**
     * 删除验证码
     *
     * @param string $identity 验证类型 [mobile, email]
     * @param string $code 验证码
     *
     * @return boolean
     */
    public function delete($identity, $code)
    {
        return $this->verifyModel->deleteAll(['identity' => $identity, 'code' => $code]) ? true : false;
    }
    
}
