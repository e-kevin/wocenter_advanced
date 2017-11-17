<?php

namespace wocenter\backend\modules\operate\controllers;

use backend\core\Controller;

/**
 * InviteUserInfoController implements the CRUD actions for InviteUserInfo model.
 */
class InviteUserInfoController extends Controller
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
