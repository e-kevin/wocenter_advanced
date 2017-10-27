<?php

namespace wocenter\backend\controllers\site\themes\adminlte\dispatches\site;

use wocenter\backend\themes\adminlte\components\Dispatch;

/**
 * 后台首页
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
