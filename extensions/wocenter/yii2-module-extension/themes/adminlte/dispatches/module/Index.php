<?php

namespace wocenter\backend\modules\extension\themes\adminlte\dispatches\module;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\Wc;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Class Index
 */
class Index extends Dispatch
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
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => Wc::$service->getExtension()->getModularity()->getModuleList(),
            'key' => 'id',
            'pagination' => [
                'pageSize' => -1, //不使用分页
            ],
        ]);
        
        Yii::$app->id = $oldAppId;
        
        return $this->display('index', [
            'dataProvider' => $dataProvider,
            'runList' => Wc::$service->getExtension()->getRunList(),
            'app' => $app,
        ]);
    }
    
}
