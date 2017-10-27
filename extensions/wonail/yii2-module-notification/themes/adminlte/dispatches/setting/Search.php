<?php

namespace wocenter\backend\modules\notification\themes\adminlte\dispatches\setting;

use wocenter\backend\modules\notification\models\NotifySearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Search
 */
class Search extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new NotifySearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
        ])->display('_search');
    }
    
}
