<?php

namespace wocenter\backend\modules\system\services;

use wocenter\core\Service;

/**
 * 系统服务类
 *
 * @property \wocenter\backend\modules\system\services\sub\ConfigService $config
 * @property \wocenter\backend\modules\system\services\sub\ValidationService $validation
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class SystemService extends Service
{
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'system';
    }
    
    /**
     * 系统配置服务类
     *
     * @return \wocenter\backend\modules\system\services\sub\ConfigService|Service
     */
    public function getConfig()
    {
        return $this->getSubService('config');
    }
    
    /**
     * 规则验证服务类
     *
     * @return \wocenter\backend\modules\system\services\sub\ValidationService|Service
     */
    public function getValidation()
    {
        return $this->getSubService('validation');
    }
    
}
