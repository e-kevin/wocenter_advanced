<?php

namespace wocenter\backend\modules\extension\services;

use wocenter\core\Service;

/**
 * 系统扩展服务类
 *
 * @property \wocenter\backend\modules\extension\services\sub\ControllerService $controller
 * @property \wocenter\backend\modules\extension\services\sub\ModularityService $modularity
 * @property \wocenter\backend\modules\extension\services\sub\LoadService $load
 * @property \wocenter\backend\modules\extension\services\sub\ThemeService $theme
 * @property \wocenter\backend\modules\extension\services\sub\DependentService $dependent
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ExtensionService extends Service
{
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'extension';
    }
    
    /**
     * 获取功能扩展子服务类
     *
     * @return \wocenter\backend\modules\extension\services\sub\ControllerService|Service
     */
    public function getController()
    {
        return $this->getSubService('controller');
    }
    
    /**
     * 管理系统模块子服务类
     *
     * @return \wocenter\backend\modules\extension\services\sub\ModularityService|Service
     */
    public function getModularity()
    {
        return $this->getSubService('modularity');
    }
    
    /**
     * 加载扩展子服务类
     *
     * @return \wocenter\backend\modules\extension\services\sub\LoadService|Service
     */
    public function getLoad()
    {
        return $this->getSubService('load');
    }
    
    /**
     * 管理系统主题子服务类
     *
     * @return \wocenter\backend\modules\extension\services\sub\ThemeService|Service
     */
    public function getTheme()
    {
        return $this->getSubService('theme');
    }
    
    /**
     * 管理扩展依赖子服务类
     *
     * @return \wocenter\backend\modules\extension\services\sub\DependentService|Service
     */
    public function getDependent()
    {
        return $this->getSubService('dependent');
    }
    
}
