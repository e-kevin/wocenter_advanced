<?php

namespace wocenter\frontend\controllers\site\controllers;

use wocenter\core\Controller;

/**
 * 首页控制器
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class SiteController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            'index',
            'about',
            'contact',
//            'error',
        ];
    }
    
}
