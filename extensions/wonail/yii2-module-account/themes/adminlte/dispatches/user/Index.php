<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use \wocenter\backend\modules\account\models\UserSearch;
use wocenter\interfaces\IdentityInterface;
use Yii;

/**
 * Class UserList
 */
class Index extends Dispatch
{
    
    public $status = IdentityInterface::STATUS_ACTIVE;
    
    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams(), ['status' => $this->status]);
        if ($searchModel->message) {
            $this->error($searchModel->message);
        }
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ])->display('index');
    }
    
}
