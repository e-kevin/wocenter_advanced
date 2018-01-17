<?php

namespace wocenter\backend\modules\extension;

use backend\core\Modularity;

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
