<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\fields;

use wocenter\backend\modules\account\models\ExtendFieldSetting;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Create
 */
class Create extends Dispatch
{
    
    /**
     * @param integer $profile_id 扩展档案ID
     *
     * @return string|\yii\web\Response
     */
    public function run($profile_id)
    {
        $model = new ExtendFieldSetting();
        $model->loadDefaultValues();
        $model->profile_id = (int)$profile_id;
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, [
                    "/{$this->controller->getUniqueId()}",
                    'profile_id' => $model->profile_id,
                ]);
            } else {
                $this->error($model->message);
            }
        }
        
        return $this->assign([
            'model' => $model,
            'profileName' => ExtendProfile::find()->where('id = :id', [
                ':id' => $model->profile_id,
            ])->select('profile_name')->scalar(),
            'formTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
        ])->display();
    }
    
}
