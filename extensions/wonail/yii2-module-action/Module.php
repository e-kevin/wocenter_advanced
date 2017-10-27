<?php

namespace wocenter\backend\modules\action;

use wocenter\backend\core\Modularity;

class Module extends Modularity
{
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\action\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'manage';
    
}
