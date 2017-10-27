<?php

namespace wocenter\backend\modules\log\themes\adminlte\dispatches\action;

use wocenter\backend\modules\log\models\ActionLogSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Index
 */
class Index extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new ActionLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        
        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ])->display();
    }
    
}
