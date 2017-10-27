<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\func;

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
        
        $model = Wc::$service->getExtension()->getController()->getControllerInfo($id, false);
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            $model->load($request->getBodyParams());
            // 是否为系统模块，以模块配置信息为准
            $model->is_system = $model->infoInstance->isSystem ?: $model->is_system;
            if ($model->save()) {
                // 调用模块内置安装方法
                $model->infoInstance->install();
                $this->success('安装成功', ["/extension/func/index", 'app' => $app]);
            } else {
                $this->error($model->message ?: '安装失败');
            }
        }
        
        Yii::$app->id = $oldAppId;
        
        return $this->assign([
            'model' => $model,
            'id' => $request->get('id'),
        ])->display();
    }
    
}
