<?php

namespace wocenter\backend\modules\extension\controllers;

use backend\core\Controller;
use yii\filters\VerbFilter;

class ModuleController extends Controller
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
                    'uninstall' => ['post'],
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
            'update',
            'install',
            'uninstall',
            'clear-cache',
        ];
    }
    
}
