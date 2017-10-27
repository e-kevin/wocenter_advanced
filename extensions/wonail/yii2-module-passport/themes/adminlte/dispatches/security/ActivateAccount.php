<?php

namespace wocenter\backend\themes\adminlte\dispatches\passport\security;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SecurityForm;
use Yii;

/**
 * Class ActivateAccount
 */
class ActivateAccount extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
            return $this->controller->goHome();
        }
        
        $model = new SecurityForm(['scenario' => SecurityForm::SCENARIO_ACTIVE_ACCOUNT]);
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->activateAccount()) {
                $this->success('系统已将一封激活邮件发送至您的邮箱，请立即查收邮件激活帐号', Yii::$app->getUser()->loginUrl, 5);
            } else {
                $this->error($model->message);
            }
        } else {
            return $this->assign('model', $model)->display();
        }
    }
    
}
