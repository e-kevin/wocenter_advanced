<?php

namespace wocenter\backend\modules\log\controllers;

use backend\core\Controller;

/**
 * ScoreController implements the CRUD actions for UserScoreLog model.
 */
class ScoreController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            'index',
            'search',
        ];
    }
    
}
