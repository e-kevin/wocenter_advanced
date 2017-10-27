<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\func;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 */
class Update extends Dispatch
{
    
    /**
     * @param string $id
     * @param string $app 应用ID
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id, $app = 'backend')
    {
        $oldAppId = Yii::$app->id;
        Yii::$app->id = $app;
        
        $model = Wc::$service->getExtension()->getController()->getControllerInfo($id);
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams())) {
                // 是否为系统模块，以模块配置信息为准
                $model->is_system = $model->infoInstance->isSystem ?: $model->is_system;
                if ($model->save()) {
                    $this->success($model->message, ["/extension/func/index", 'app' => $app]);
                } else {
                    $this->error($model->message);
                }
            } else {
                if ($model->getDirtyAttributes()) {
                    $this->error($model->message);
                } else {
                    $this->success($model->message, ["/extension/func/index", 'app' => $app]);
                }
            }
        }
        
        Yii::$app->id = $oldAppId;
        
        return $this->assign([
            'model' => $model,
            'id' => $request->get('id'),
        ])->display();
    }
    
}
