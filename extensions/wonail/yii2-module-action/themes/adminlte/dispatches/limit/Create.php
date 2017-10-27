<?php

namespace wocenter\backend\modules\action\themes\adminlte\dispatches\limit;

use wocenter\backend\modules\action\models\Action;
use wocenter\backend\modules\action\models\ActionLimit;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 */
class Create extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $model = new ActionLimit();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"]);
            } else {
                $this->error($model->message);
            }
        }
        
        return $this->assign([
            'model' => $model,
            'actionList' => (new Action())->getSelectList('name', 'title'),
        ])->display();
    }
    
}
