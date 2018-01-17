<?php

namespace wocenter\backend\modules\system\controllers;

use wocenter\core\Controller;

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
                'class' => 'wocenter\actions\FlushCache',
            ],
        ];
    }
    
}
