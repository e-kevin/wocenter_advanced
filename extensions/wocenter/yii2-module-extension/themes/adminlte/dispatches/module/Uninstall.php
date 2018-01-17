<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\module;

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
        $model = Wc::$service->getExtension()->getModularity()->getModuleInfo($id);
        $res = $model->delete();
        Yii::$app->id = $oldAppId;
        if ($res) {
            $this->success('卸载成功', parent::RELOAD_FULL_PAGE);
        } else {
            $this->error($model->message ? nl2br($model->message) : '卸载失败');
        }
    }
    
}
