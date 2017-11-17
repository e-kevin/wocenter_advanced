<?php

namespace wocenter\backend\controllers\admin\controllers;

use backend\core\Controller;
use Yii;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for BackendUser model.
 */
class AdminController extends Controller
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
                    'relieve' => ['post'],
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
            'relieve',
            'add',
            'update',
        ];
    }
    
}
