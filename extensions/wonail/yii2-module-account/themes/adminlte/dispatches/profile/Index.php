<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\profile;

use wocenter\backend\modules\account\models\ExtendProfileSearch;
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
        $searchModel = new ExtendProfileSearch();
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
