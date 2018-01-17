<?php

namespace wocenter\backend\modules\menu\themes\adminlte\dispatches\category;

use wocenter\backend\modules\menu\models\MenuCategory;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 */
class Create extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new MenuCategory();
        $model->loadDefaultValues();
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
        ])->display();
    }
    
}
