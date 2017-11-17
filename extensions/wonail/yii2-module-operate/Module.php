<?php

namespace wocenter\backend\modules\operate;

use backend\core\Modularity;

class Module extends Modularity
{
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\operate\controllers';
    
    /**
     * @inheritdoc
     */
    public $defaultRoute = 'invite';
    
}