<?php

namespace wocenter\backend\modules\operate\controllers;

use wocenter\actions\DeleteOne;
use wocenter\actions\MultipleDelete;
use backend\core\Controller;
use wocenter\backend\modules\operate\models\InviteLog;
use yii\filters\VerbFilter;

/**
 * InviteLogController implements the CRUD actions for InviteLog model.
 */
class InviteLogController extends Controller
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
                'modelClass' => InviteLog::className(),
            ],
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => InviteLog::className(),
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
