<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\func;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;

/**
 * Class ClearCache
 */
class ClearCache extends Dispatch
{
    
    /**
     * @param string $app 应用ID
     *
     * @return string|\yii\web\Response
     */
    public function run($app = 'backend')
    {
        $oldAppId = Yii::$app->id;
        Yii::$app->id = $app;
        
        Wc::$service->getExtension()->getController()->clearCache();
        
        Yii::$app->id = $oldAppId;
        
        $this->success('清理成功', Dispatch::RELOAD_PAGE);
    }
    
}
