<?php

namespace wocenter\backend\modules\data\themes\adminlte\dispatches\areaRegion;

use wocenter\backend\modules\data\models\AreaRegionSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Index
 */
class Index extends Dispatch
{
    
    /**
     * @param integer $pid
     *
     * @return string|\yii\web\Response
     */
    public function run($pid = 0)
    {
        $searchModel = new AreaRegionSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        
        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }
        
        $breadcrumbs = $searchModel->getBreadcrumbs($pid, '区域管理', '/data/area-region/index');
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pid' => $pid,
            'breadcrumbs' => $breadcrumbs,
            'title' => $breadcrumbs[$pid]['label'],
        ])->display();
    }
    
}
