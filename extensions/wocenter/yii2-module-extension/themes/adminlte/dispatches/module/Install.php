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
     * @param string $id 扩展名称
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
        if (array_key_exists($id, Wc::$service->getExtension()->getLoad()->getInstalled())) {
            return $this->controller->redirect(["update", 'app' => $app, 'id' => $id]);
        }
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            $model->load($request->getBodyParams());
            // 是否为系统模块，以模块配置信息为准
            $model->is_system = $model->infoInstance->isSystem ?: $model->is_system;
            if ($model->save()) {
                $this->success('安装成功', parent::RELOAD_FULL_PAGE);
            } else {
                $this->error($model->message ? nl2br($model->message) : '安装失败');
            }
        }
        
        Yii::$app->id = $oldAppId;
        
        return $this->assign([
            'model' => $model,
            'runModuleList' => $model->getRunList(),
            'id' => $request->get('id'),
            'dependList' => Wc::$service->getExtension()->getDependent()->getList($id),
        ])->display();
    }
    
}
