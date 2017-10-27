<?php

namespace wocenter\backend\modules\passport\themes\adminlte\dispatches\common;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SignupForm;
use Yii;

/**
 * Class InviteSignup
 */
class InviteSignup extends Dispatch
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
        
        $model = new SignupForm(['scenario' => SignupForm::SCENARIO_SIGNUP_BY_INVITE]);
        $thankMessage = Yii::t('wocenter/app', 'Thank you for your support, the system has now suspended the registration of new users.');
        if (empty($model->getRegisterSwitch())) {
            $this->error($thankMessage);
        }
        $request = Yii::$app->getRequest();
        
        // 邀请注册
        if ($model->getIsInviteRegister()) {
            if ($request->getIsPost()) {
                if ($model->load($request->getBodyParams()) && $model->validate()) {
                    $this->success('验证成功，正在为您跳转到注册页面～', ['/passport/common/signup', 'code' => $model->code], 2);
                } else {
                    $this->error($model->message);
                }
            } else {
                // 邀请注册页面如果获取到`邀请码`，则自动跳转到注册页面
                $code = $request->getQueryParam('code', 0);
                if ($code) {
                    return $this->controller->redirect(['/passport/common/signup', 'code' => $code]);
                }
                
                return $this->assign('model', $model)->display();
            }
        } // 普通注册
        elseif ($model->getIsNormalRegister()) {
            // todo 是否更改为直接跳转，提升用户体验？
            $this->error('系统已暂停【邀请】方式进行注册，正在为您跳转到普通注册页面～', ['/passport/common/signup']);
        } else {
            $this->error($thankMessage);
        }
    }
    
}