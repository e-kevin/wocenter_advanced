<?php

namespace wocenter\backend\modules\system\themes\adminlte\dispatches\configManager;

use wocenter\backend\modules\system\models\ConfigSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
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
        $searchModel = new ConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        
        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'configGroupList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_GROUP_LIST'),
            'configTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
        ])->display();
    }
    
}
