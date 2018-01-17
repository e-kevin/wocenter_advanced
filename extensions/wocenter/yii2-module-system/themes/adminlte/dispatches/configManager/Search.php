<?php

namespace wocenter\backend\modules\system\themes\adminlte\dispatches\configManager;

use wocenter\backend\modules\system\models\ConfigSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\libs\Constants;
use wocenter\Wc;
use Yii;
use yii\helpers\ArrayHelper;

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
        $searchModel = new ConfigSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'configGroupList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_GROUP_LIST'),
            'configTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
            'statusList' => ArrayHelper::merge(
                [Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')],
                Constants::getStatusList()
            ),
        ])->display('_search');
    }
    
}
