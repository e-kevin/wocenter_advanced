<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\identity;

use wocenter\backend\modules\account\models\IdentityGroup;
use wocenter\backend\modules\account\models\IdentitySearch;
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
        $searchModel = new IdentitySearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'identityGroup' => (new IdentityGroup())->getSelectList(),
        ])->display('_search');
    }
    
}
