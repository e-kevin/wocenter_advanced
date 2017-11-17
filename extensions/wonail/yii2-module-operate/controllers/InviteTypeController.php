<?php

namespace wocenter\backend\modules\operate\controllers;

use wocenter\actions\DeleteOne;
use backend\core\Controller;
use wocenter\backend\modules\operate\models\InviteType;
use yii\filters\VerbFilter;

/**
 * InviteTypeController implements the CRUD actions for InviteType model.
 */
class InviteTypeController extends Controller
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
                'modelClass' => InviteType::className(),
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
            'create',
            'update',
        ];
    }
    
}
