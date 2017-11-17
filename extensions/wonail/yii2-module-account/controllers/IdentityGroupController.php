<?php

namespace wocenter\backend\modules\account\controllers;

use wocenter\actions\DeleteOne;
use backend\core\Controller;
use wocenter\backend\modules\account\models\IdentityGroup;
use yii\filters\VerbFilter;

/**
 * IdentityGroupController implements the CRUD actions for IdentityGroup model.
 */
class IdentityGroupController extends Controller
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
                'modelClass' => IdentityGroup::className(),
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
