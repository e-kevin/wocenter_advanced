<?php

namespace wocenter\backend\modules\account\controllers;

use backend\core\Controller;
use yii\filters\VerbFilter;

/**
 * IdentityUserController implements the CRUD actions for UserIdentity model.
 */
class IdentityUserController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'active' => ['post'],
                    'forbidden' => ['post'],
                ],
            ],
        ];
    }
    
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
