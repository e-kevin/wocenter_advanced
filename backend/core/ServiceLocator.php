<?php

namespace backend\core;

use wocenter\backend\modules\{
    menu\services\MenuService, extension\services\ExtensionService, system\services\SystemService,
    account\services\AccountService, action\services\ActionService, log\services\LogService,
    notification\services\NotificationService, passport\services\PassportService
};
use wocenter\core\Service;


/**
 * 系统服务定位器，主要作用有：
 * 1. 检测服务组件是否符合WoCenter的服务类标准。
 * 2. 支持IDE代码提示功能，方便开发。
 * 3. 支持`Yii::trace()`调试信息。
 *
 * @property AccountService $account
 * @property ActionService $action
 * @property LogService $log
 * @property MenuService $menu
 * @property NotificationService $notification
 * @property PassportService $passport
 * @property SystemService $system
 * @property ExtensionService $extension
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ServiceLocator extends \wocenter\core\ServiceLocator
{
    
    /**
     * 用户管理服务类
     *
     * @return AccountService|Service
     */
    public function getAccount()
    {
        return $this->getService('account');
    }
    
    /**
     * 系统服务类
     *
     * @return SystemService|Service
     */
    public function getSystem()
    {
        return $this->getService('system');
    }
    
    /**
     * 行为管理服务类
     *
     * @return ActionService|Service
     */
    public function getAction()
    {
        return $this->getService('action');
    }
    
    /**
     * 日志管理服务类
     *
     * @return LogService|Service
     */
    public function getLog()
    {
        return $this->getService('log');
    }
    
    /**
     * 系统通知服务类
     *
     * @return NotificationService|Service
     */
    public function getNotification()
    {
        return $this->getService('notification');
    }
    
    /**
     * 系统通行证服务类
     *
     * @return PassportService|Service
     */
    public function getPassport()
    {
        return $this->getService('passport');
    }
    
    /**
     * 菜单管理服务类
     *
     * @return MenuService|Service
     */
    public function getMenu()
    {
        return $this->getService('menu');
    }
    
    /**
     * 系统扩展服务类
     *
     * @return ExtensionService|Service
     */
    public function getExtension()
    {
        return $this->getService('extension');
    }
    
}
