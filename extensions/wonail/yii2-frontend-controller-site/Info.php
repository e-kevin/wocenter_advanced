<?php

namespace wocenter\frontend\controllers\site;

use wocenter\core\FunctionInfo;

class Info extends FunctionInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'frontend';
    
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
    public $description = '提供前台首页访问等操作';
    
    /**
     * @inheritdoc
     */
    public $isSystem = true;
    
}
