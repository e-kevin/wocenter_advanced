<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\identityUser;

use wocenter\backend\modules\account\models\UserIdentitySearch;
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
        $searchModel = new UserIdentitySearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
        ])->display('_search');
    }
    
}
