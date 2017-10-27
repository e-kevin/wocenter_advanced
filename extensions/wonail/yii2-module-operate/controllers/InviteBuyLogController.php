<?php

namespace wocenter\backend\modules\operate\controllers;

use wocenter\backend\core\Controller;

/**
 * InviteBuyLogController implements the CRUD actions for InviteBuyLog model.
 */
class InviteBuyLogController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            'index',
            'search',
            'create',
            'update',
        ];
    }
    
}
