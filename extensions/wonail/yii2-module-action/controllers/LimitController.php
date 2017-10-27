<?php

namespace wocenter\backend\modules\action\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\action\models\ActionLimit;

/**
 * ActionLimitController implements the CRUD actions for ActionLimit model.
 */
class LimitController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => '\yii\filters\VerbFilter',
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => ActionLimit::className(),
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
            'create',
            'update',
        ];
    }
    
}
