<?php

namespace wocenter\backend\modules\system\controllers;

use backend\core\Controller;

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
