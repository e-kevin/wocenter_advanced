<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\fields;

use wocenter\backend\modules\account\models\ExtendFieldSetting;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 */
class Update extends Dispatch
{
    
    use LoadModelTrait;
    
    /**
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var ExtendFieldSetting $model */
        $model = $this->loadModel(ExtendFieldSetting::className(), $id);
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
