<?php

namespace wocenter\frontend\controllers\site\themes\basic\dispatches\site;

use wocenter\core\Dispatch;

/**
 * 首页
 */
class Index extends Dispatch
{
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->display();
    }
    
}
