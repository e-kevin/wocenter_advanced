<?php

namespace wocenter\backend\modules\account\controllers;

use wocenter\backend\actions\DeleteOne;
use wocenter\backend\actions\MultipleDelete;
use wocenter\backend\core\Controller;
use wocenter\backend\modules\data\models\Tag;
use yii\filters\VerbFilter;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends Controller
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
                'modelClass' => Tag::className(),
            ],
            'delete' => [
                'class' => DeleteOne::className(),
                'modelClass' => Tag::className(),
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
