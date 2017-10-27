<?php

namespace wocenter\backend\modules\menu\themes\adminlte\dispatches\category;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;

/**
 * Class Create
 */
class SyncMenus extends Dispatch
{
    
    public function run()
    {
        if (Wc::$service->getMenu()->syncMenus()) {
            $this->success('菜单同步成功', self::RELOAD_FULL_PAGE);
        } else {
            $this->error('菜单同步失败', self::RELOAD_FULL_PAGE);
        }
    }
    
}
