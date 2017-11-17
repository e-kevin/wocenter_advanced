<?php

namespace wocenter\backend\modules\notification\services\notification;

use wocenter\backend\modules\notification\services\NotificationService;
use wocenter\core\Service;

/**
 * 短信服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class SmsService extends Service
{
    
    /**
     * @var NotificationService 父级服务类
     */
    public $service;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'sms';
    }
    
    /**
     * 发送短信
     * todo 添加手机发送短信功能
     *
     * @param string $template 模板文件
     * @param array $params 模板变量
     *  - title
     *  - content
     * @param string $to 接收手机号码
     * @param string $subject 邮箱主题
     * @param string $from 邮箱发送者
     *
     * @return boolean
     */
    public function send($template, $params, $to, $subject = '', $from = 'system')
    {
        return true;
    }
    
}
