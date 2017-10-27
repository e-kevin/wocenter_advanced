<?php

namespace wocenter\backend\modules\system\controllers;

use wocenter\backend\core\Controller;

/**
 * Class CacheController
 */
class CacheController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'flushCache' => [
                'class' => 'wocenter\backend\actions\FlushCache',
            ],
        ];
    }
    
}
