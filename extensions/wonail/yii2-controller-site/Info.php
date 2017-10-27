<?php

namespace wocenter\backend\controllers\site;

use wocenter\core\FunctionInfo;

class Info extends FunctionInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'backend';
    
    /**
     * @inheritdoc
     */
    public $moduleId = '';
    
    /**
     * @inheritdoc
     */
    public $name = '首页';
    
    /**
     * @inheritdoc
     */
    public $description = '提供后台首页访问、错误控制器等操作';
    
    /**
     * @inheritdoc
     */
    public $isSystem = true;
    
}
