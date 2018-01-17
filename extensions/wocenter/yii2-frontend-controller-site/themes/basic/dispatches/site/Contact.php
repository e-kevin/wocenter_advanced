<?php

namespace wocenter\frontend\controllers\site\themes\basic\dispatches\site;

use wocenter\core\Dispatch;

/**
 * 联系
 */
class Contact extends Dispatch
{
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->display();
    }
    
}
