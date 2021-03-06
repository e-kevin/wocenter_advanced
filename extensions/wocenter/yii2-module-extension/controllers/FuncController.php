<?php

namespace wocenter\backend\modules\extension\controllers;

use wocenter\core\Controller;
use yii\filters\VerbFilter;

class FuncController extends Controller
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
