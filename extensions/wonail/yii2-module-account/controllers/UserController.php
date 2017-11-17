<?php

namespace wocenter\backend\modules\account\controllers;

use backend\core\Controller;
use wocenter\backend\modules\account\models\BaseUser;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for BaseUser model.
 */
class UserController extends Controller
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
                    'init-password' => ['post'],
                    'generate' => ['post'],
                    'delete' => ['post'],
                    'active' => ['post'],
                    'forbidden' => ['post'],
                    'unlock' => ['post'],
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
            'forbidden-list' => [
                'dispatchOptions' => [
                    'map' => 'index',
                ],
                'status' => BaseUser::STATUS_FORBIDDEN,
            ],
            'locked-list' => [
                'dispatchOptions' => [
                    'map' => 'index',
                ],
                'status' => BaseUser::STATUS_LOCKED,
            ],
            'delete' => [
                'dispatchOptions' => [
                    'map' => 'change-status',
                ],
                'method' => 'delete',
            ],
            'forbidden' => [
                'dispatchOptions' => [
                    'map' => 'change-status',
                ],
                'method' => 'forbidden',
            ],
            'active' => [
                'dispatchOptions' => [
                    'map' => 'change-status',
                ],
                'method' => 'active',
            ],
            'unlock' => [
                'dispatchOptions' => [
                    'map' => 'change-status',
                ],
                'method' => 'unlock',
            ],
            'view',
            'update',
            'search',
            'generate',
            'init-password',
            'profile',
        ];
    }
    
}
