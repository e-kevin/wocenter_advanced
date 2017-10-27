<?php

namespace wocenter\frontend\controllers\site\themes\basic\dispatches\site;

use wocenter\core\Dispatch;

/**
 * 关于
 */
class About extends Dispatch
{
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->display();
    }
    
}
