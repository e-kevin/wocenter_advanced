<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\module;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 */
class Install extends Dispatch
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
        
        $model = Wc::$service->getExtension()->getModularity()->getModuleInfo($id, false);
        $request = Yii::$app->getRequest();
        $validRunModuleList = $model->getValidRunList();
        
        if ($request->getIsPost()) {
            $model->load($request->getBodyParams());
            // 是否为系统模块，以模块配置信息为准
            $model->is_system = $model->infoInstance->isSystem ?: $model->is_system;
            // 调用模块内置安装方法
            if (!$model->infoInstance->install()) {
                $this->error(Wc::getErrorMessage());
            }
            if ($model->save()) {
                $this->success('安装成功', ["/{$this->controller->module->id}", 'app' => $app]);
            } else {
                $this->error($model->message);
            }
        }
        
        Yii::$app->id = $oldAppId;
        
        return $this->assign([
            'model' => $model,
            'validRunModuleList' => $validRunModuleList,
            'id' => $request->get('id'),
        ])->display();
    }
    
}
