<?php

namespace wocenter\console\controllers\wocenter;

use wocenter\core\FunctionInfo;

class Info extends FunctionInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'console';
    
    /**
     * @inheritdoc
     */
    public $moduleId = '';
    
    /**
     * @inheritdoc
     */
    public $name = 'WoCenter控制台命令';
    
    /**
     * @inheritdoc
     */
    public $description = '提供WoCenter安装、卸载等控制台命令';
    
    /**
     * @inheritdoc
     */
    public $isSystem = true;
    
}
