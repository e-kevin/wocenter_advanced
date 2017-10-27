<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\identity;

use wocenter\backend\modules\account\models\IdentitySearch;
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
        $searchModel = new IdentitySearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        
        if ($searchModel->message) {
            $this->error($searchModel->message);
        }
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ])->display();
    }
    
}
