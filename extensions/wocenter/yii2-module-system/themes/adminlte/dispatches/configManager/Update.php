<?php

namespace wocenter\backend\modules\system\themes\adminlte\dispatches\configManager;

use wocenter\backend\modules\system\models\Config;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use wocenter\Wc;
use Yii;

/**
 * Class Index
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
        /** @var Config $model */
        $model = $this->loadModel(Config::className(), $id);
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->module->id}"]);
            } else {
                $this->error($model->message);
            }
        }
        
        return $this->assign([
            'model' => $model,
            'configGroupList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_GROUP_LIST'),
            'configTypeList' => Wc::$service->getSystem()->getConfig()->extra('CONFIG_TYPE_LIST'),
        ])->display();
    }
    
}
