<?php

namespace frontend\core;

use wocenter\backend\modules\extension\services\ExtensionService;
use wocenter\backend\modules\system\services\SystemService;
use wocenter\core\Service;

/**
 * 系统服务定位器，主要作用有：
 * 1. 检测服务组件是否符合WoCenter的服务类标准。
 * 2. 支持IDE代码提示功能，方便开发。
 * 3. 支持`Yii::trace()`调试信息。
 *
 * @property ExtensionService $extension
 * @property SystemService $system
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
     * 系统服务类
     *
     * @return SystemService|Service
     */
    public function getSystem()
    {
        return $this->getService('system');
    }
    
}
