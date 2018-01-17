<?php

namespace command\core;

use wocenter\backend\modules\extension\services\ExtensionService;
use wocenter\backend\modules\menu\services\MenuService;
use wocenter\core\Service;


/**
 * 系统服务定位器，主要作用有：
 * 1. 检测服务组件是否符合WoCenter的服务类标准。
 * 2. 支持IDE代码提示功能，方便开发。
 * 3. 支持`Yii::trace()`调试信息。
 *
 * @property ExtensionService $extension
 * @property MenuService $menu
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ServiceLocator extends \wocenter\core\ServiceLocator
{
    
    /**
     * 系统扩展服务类
     *
     * @return ExtensionService|Service
     */
    public function getExtension()
    {
        return $this->getService('extension');
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
    
}
