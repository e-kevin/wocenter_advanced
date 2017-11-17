<?php

namespace wocenter\backend\modules\system;

use backend\core\Modularity;

class Module extends Modularity
{
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\system\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'config-manager';
    
}
