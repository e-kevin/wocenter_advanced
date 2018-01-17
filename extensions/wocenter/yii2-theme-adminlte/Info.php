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
    public $name = 'AdminLTE2';
    
    /**
     * @inheritdoc
     */
    public $description = 'AdminLTE主题';
    
    /**
     * @inheritdoc
     */
    public $dispatch = '\wocenter\backend\themes\adminlte\components\Dispatch';
    
    /**
     * @inheritdoc
     */
    public $isSystem = true;
    
    /**
     * @inheritdoc
     */
    protected $depends = [
        'wocenter/yii2-module-system:dev-master',
        'wocenter/yii2-module-extension:dev-master',
        'wocenter/yii2-module-menu:dev-master',
    ];
    
}
