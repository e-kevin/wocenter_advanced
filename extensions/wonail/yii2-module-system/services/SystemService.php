<?php

namespace wocenter\backend\modules\system\services;

use wocenter\core\Service;

/**
 * 系统服务类
 *
 * @property \wocenter\backend\modules\system\services\system\ConfigService $config
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
     * @return \wocenter\backend\modules\system\services\system\ConfigService|Service
     */
    public function getConfig()
    {
        return $this->getSubService('config');
    }
    
}
