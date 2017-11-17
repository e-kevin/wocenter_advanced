<?php

namespace wocenter\backend\modules\log\controllers;

use wocenter\actions\DeleteOne;
use wocenter\actions\MultipleDelete;
use backend\core\Controller;
use wocenter\backend\modules\log\models\ActionLog;
use yii\filters\VerbFilter;

/**
 * ActionLogController implements the CRUD actions for ActionLog model.
 */
class ActionController extends Controller
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
                    'delete' => ['post'],
                    'batch-delete' => ['post'],
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
            'batch-delete' => [
                'class' => MultipleDelete::className(),
                'modelClass' => ActionLog::className(),
            ],
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => ActionLog::className(),
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
