<?php

namespace wocenter\backend\modules\notification\services;

use wocenter\backend\modules\notification\models\Notify;
use wocenter\core\Service;
use wocenter\Wc;
use Yii;

/**
 * 系统通知服务类
 *
 * @property \wocenter\backend\modules\notification\services\notification\EmailService $email
 * @property \wocenter\backend\modules\notification\services\notification\SmsService $sms
 * @property \wocenter\backend\modules\notification\services\notification\MessageService $message
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class NotificationService extends Service
{
    
    /**
     * @var string|array|callable|Notify 通知模型
     */
    public $notifyModel = '\wocenter\backend\modules\notification\models\Notify';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'notification';
    }
    
    /**
     * 发送系统通知，包括消息、邮件，仅支持单用户
     *
     * @param string $node 节点Key值
     * @param integer $toUid 接收通知的用户标识
     * @param array $params 语言变量
     * @param string $template 模板文件 系统会根据不同消息类型选择合适的模板文件，后缀类型：-html|-text
     *
     * @return bool
     */
    public function sendNotify($node, $toUid, array $params = [], $template = 'main')
    {
        /** @var Notify $notifyModel */
        $notifyModel = Yii::createObject($this->notifyModel);
        $notifyConfig = $notifyModel->getValue($node);
        if (is_null($notifyConfig)) {
            $this->_info = $notifyModel->message;
            $this->_status = false;
            
            return $this->_status;
        }
        // 获取用户
        $user = Wc::$service->getAccount()->info($toUid);
        if ($user == null) {
            $this->_info = Yii::t('wocenter/app', 'User does not exist.');
            $this->_status = false;
            
            return $this->_status;
        }
        
        // 发送邮件通知
        if ($notifyConfig['email_sender']) {
            $this->getEmail()->send($template . '-html', [
                'title' => $notifyConfig['name'],
                'content' => Yii::t('wocenter/app', $notifyConfig['content_key'], $params),
            ], $user[2], Yii::t('wocenter/app', $notifyConfig['name_key']), $notifyConfig['email_sender']);
        }
        // 发送系统消息
        if ($notifyConfig['send_message']) {
            $this->getMessage()->send(
                $toUid, 0, $notifyConfig['name'], Yii::t('wocenter/app', $notifyConfig['content_key'], $params)
            );
        }
        
        return true;
    }
    
    /**
     * 获取邮件服务类
     *
     * @return \wocenter\backend\modules\notification\services\notification\EmailService|Service
     */
    public function getEmail()
    {
        return $this->getSubService('email');
    }
    
    /**
     * 获取短信服务类
     *
     * @return \wocenter\backend\modules\notification\services\notification\SmsService|Service
     */
    public function getSms()
    {
        return $this->getSubService('sms');
    }
    
    /**
     * 获取系统消息服务类
     *
     * @return \wocenter\backend\modules\notification\services\notification\MessageService|Service
     */
    public function getMessage()
    {
        return $this->getSubService('message');
    }
    
}
