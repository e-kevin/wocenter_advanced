<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SecurityForm;
use Yii;

/**
 * Class InitPassword
 */
class InitPassword extends Dispatch
{
    
    public function run()
    {
        $selections = Yii::$app->getRequest()->getBodyParam('selection');
        $model = new SecurityForm();
        if ($model->initPassword($selections)) {
            $this->success($model->message, '', 3);
        } else {
            $this->error($model->message);
        }
    }
    
}
