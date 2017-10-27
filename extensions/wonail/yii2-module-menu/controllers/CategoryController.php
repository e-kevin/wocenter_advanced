<?php

namespace wocenter\backend\modules\menu\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\menu\models\MenuCategory;
use yii\filters\VerbFilter;

/**
 * CategoryController implements the CRUD actions for MenuCategory model.
 */
class CategoryController extends Controller
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
                    'sync-menus' => ['post'],
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
                'modelClass' => MenuCategory::className(),
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
            'sync-menus',
        ];
    }
    
}
