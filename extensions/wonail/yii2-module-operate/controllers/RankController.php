<?php

namespace wocenter\backend\modules\operate\controllers;

use wocenter\actions\DeleteOne;
use backend\core\Controller;
use wocenter\backend\modules\operate\models\Rank;
use yii\filters\VerbFilter;

/**
 * RankController implements the CRUD actions for Rank model.
 */
class RankController extends Controller
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
                'modelClass' => Rank::className(),
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
