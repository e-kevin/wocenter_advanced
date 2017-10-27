<?php

namespace wocenter\backend\themes\adminlte\dispatches\passport\security;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SecurityForm;
use Yii;

/**
 * Class ChangePassword
 */
class ChangePassword extends Dispatch
{
    
    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        $model = new SecurityForm([
            'scenario' => SecurityForm::SCENARIO_CHANGE_PASSWORD,
        ]);
        if ($model->load($request->getBodyParams()) && $model->changePassword()) {
            $this->success($model->message);
        } else {
            $this->error($model->message);
        }
    }
    
}
