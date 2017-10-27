<?php

namespace wocenter\backend\modules\data\themes\adminlte\dispatches\areaRegion;

use wocenter\backend\modules\data\models\AreaRegionSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Search
 */
class Search extends Dispatch
{
    
    /**
     * @param integer $pid
     *
     * @return string|\yii\web\Response
     */
    public function run($pid = 0)
    {
        $searchModel = new AreaRegionSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
        ])->display('_search');
    }
    
}
