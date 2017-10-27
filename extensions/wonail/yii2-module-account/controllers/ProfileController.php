<?php

namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\account\models\ExtendProfile;
use yii\filters\VerbFilter;

/**
 * ProfileController implements the CRUD actions for FieldGroup model.
 */
class ProfileController extends Controller
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
                'modelClass' => ExtendProfile::className(),
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
