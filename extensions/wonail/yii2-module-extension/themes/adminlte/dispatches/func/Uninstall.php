<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\func;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class Update
 */
class Uninstall extends Dispatch
{
    
    /**
     * @param string $id
     * @param string $app 应用ID
     *
     * @throws \Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id, $app = 'backend')
    {
        $oldAppId = Yii::$app->id;
        Yii::$app->id = $app;
        
        $model = Wc::$service->getExtension()->getController()->getControllerInfo($id);
        
        if ($model->infoInstance->canUninstall) {
            // 调用模块内置卸载方法
            if (!$model->infoInstance->uninstall()) {
                $this->error(Wc::getErrorMessage());
            }
            $res = $model->delete();
            Yii::$app->id = $oldAppId;
            if ($res) {
                $this->success('卸载成功', parent::RELOAD_PAGE);
            } else {
                $this->error('卸载失败');
            }
        } else {
            $this->error($id . ' 功能扩展属于系统扩展，暂不支持卸载');
        }
    }
    
}
