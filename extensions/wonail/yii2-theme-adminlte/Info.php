<?php

namespace wocenter\backend\themes\adminlte;

use wocenter\core\ThemeInfo;

class Info extends ThemeInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'backend';
    
    /**
     * @inheritdoc
     */
    public $id = 'adminlte';
    
    /**
     * @inheritdoc
     */
    public $name = 'AdminLTE主题';
    
    /**
     * @inheritdoc
     */
    public $description = 'AdminLTE主题';
    
    /**
     * @inheritdoc
     */
    public $isSystem = true;
    
    /**
     * @inheritdoc
     */
    public $dispatch = '\wocenter\backend\themes\adminlte\components\Dispatch';
    
}
