<?php

namespace wocenter\backend\themes\adminlte\dispatches\passport\security;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SecurityForm;
use Yii;

/**
 * Class ActivateUser
 */
class ActivateUser extends Dispatch
{
    
    /**
     * @return \yii\web\Response
     */
    public function run()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->controller->goHome();
        }
        
        $model = new SecurityForm();
        $code = Yii::$app->getRequest()->getQueryParam('code', 0);
        if ($model->activateUser($code)) {
            $this->success('帐号已成功激活', Yii::$app->getUser()->loginUrl);
        } else {
            $this->error($model->message, 'activate-account', 2);
        }
    }
    
}
