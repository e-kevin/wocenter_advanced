<?php

namespace wocenter\backend\modules\extension;

use wocenter\backend\core\Modularity;

class Module extends Modularity
{
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\extension\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'module';
    
}
