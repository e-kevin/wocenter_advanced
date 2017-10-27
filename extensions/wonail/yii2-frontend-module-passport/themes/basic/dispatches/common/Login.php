<?php

namespace wocenter\frontend\modules\passport\themes\basic\dispatches\common;

use wocenter\core\Dispatch;
use wocenter\backend\modules\passport\models\LoginForm;
use Yii;

/**
 * Class Login
 */
class Login extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        if (Yii::$app->getSession()->has(LoginForm::TMP_LOGIN)) {
            return $this->controller->redirect('step');
        }
        
        if (!Yii::$app->user->isGuest) {
            return $this->controller->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->getBodyParams())) {
            if ($model->login()) {
                if ($model->message === LoginForm::NEED_STEP) {
                    return $this->success('正在为您跳转至步骤页面~', 'step');
                } else {
                    return $this->controller->goBack();
                }
            } else {
                return $this->error($model->message);
            }
        } else {
            return $this->assign([
                'model' => $model,
            ])->display();
        }
    }
    
}