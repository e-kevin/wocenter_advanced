<?php

namespace wocenter\backend\modules\system\themes\adminlte\dispatches\setting;

use wocenter\backend\modules\system\models\ConfigForm;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use Yii;

/**
 * Class Update
 */
class Update extends Dispatch
{
    
    use LoadModelTrait;
    
    public $group = 1;
    
    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $models = new ConfigForm(['categoryGroup' => $this->group]);
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            if ($models->load($request->getBodyParams()) && $models->save()) {
                $this->success(Yii::t('wocenter/app', 'Saved successful.'), ["{$this->controller->action->id}"]);
            } else {
                $this->error($models->message ?: Yii::t('wocenter/app', 'Saved failure.'));
            }
        }
        
        return $this->assign([
            'models' => $models,
            'id' => $this->group,
        ])->display('update');
    }
    
}
