<?php

namespace wocenter\backend\modules\action\themes\adminlte\dispatches\manage;

use wocenter\backend\modules\action\models\ActionSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
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
        $searchModel = new ActionSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'installedModuleSelectList' => Wc::$service->getExtension()->getModularity()->getInstalledSelectList(),
        ])->display('_search');
    }
    
}
