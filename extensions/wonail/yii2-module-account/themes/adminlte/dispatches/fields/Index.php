<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\fields;

use wocenter\backend\modules\account\models\ExtendFieldSettingSearch;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Index
 */
class Index extends Dispatch
{
    
    /**
     * @param integer $profile_id
     *
     * @return string|\yii\web\Response
     */
    public function run($profile_id = 0)
    {
        $searchModel = new ExtendFieldSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        
        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'profileId' => $profile_id,
            'profileName' => ExtendProfile::find()->where('id = :id', [
                ':id' => $profile_id,
            ])->select('profile_name')->scalar(),
            'formTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
        ])->display();
    }
    
}
