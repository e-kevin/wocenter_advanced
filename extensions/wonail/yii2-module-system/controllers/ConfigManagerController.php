<?php

namespace wocenter\backend\modules\system\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\system\models\Config;
use yii\filters\VerbFilter;

/**
 * Class ConfigManagerController
 */
class ConfigManagerController extends Controller
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
                'modelClass' => Config::className(),
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
            'view',
            'create',
            'update',
        ];
    }
    
}
