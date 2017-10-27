<?php

namespace wocenter\backend\modules\passport\themes\adminlte\dispatches\common;

use wocenter\backend\modules\account\models\UserIdentity;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\backend\modules\passport\models\FlowAvatarForm;
use wocenter\backend\modules\passport\models\FlowProfileForm;
use wocenter\backend\modules\passport\models\FlowTagForm;
use wocenter\backend\modules\passport\models\LoginForm;
use wocenter\Wc;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class Step
 */
class Step extends Dispatch
{
    
    /**
     * @var BaseUser 当前登录用户
     */
    protected $_user;
    
    /**
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        /**
         * @var array $tmpLogin 获取SESSION数据，数据来源在UserIdentity()->needStep()里设置
         * @see UserIdentity::needStep()
         */
        $tmpLogin = Yii::$app->getSession()->get(LoginForm::TMP_LOGIN);
        // $_SESSION参数不存在或已过期则跳转至登陆页面
        if (empty($tmpLogin)) {
            return $this->controller->redirect(Yii::$app->getUser()->loginUrl);
        }
        $uid = $tmpLogin['uid'];
        $identityId = $tmpLogin['identityId'];
        $identity = $tmpLogin['identity'];
        $rememberMe = $tmpLogin['rememberMe'];
        $ucenterService = Wc::$service->getPassport()->getUcenter();
        // 是否开启注册步骤
        $registerStepSwitch = Wc::$service->getSystem()->getConfig()->kanban('REGISTER_STEP');
        $userIdentityModel = new UserIdentity();
        $userStep = $userIdentityModel->getUserStep($uid, $identityId);
        // 没有开启步骤流程、步骤不存在或步骤已结束则直接登录
        if (empty($registerStepSwitch) || empty($userStep) || $userStep['step'] == UserIdentity::STEP_FINISHED) {
            // 快速登录
            quickLogin:
            if ($ucenterService->quickLogin($this->_user ?: $identity, $rememberMe)) {
                // 删除SESSION
                Yii::$app->getSession()->remove(LoginForm::TMP_LOGIN);
                
                $this->success('正在为您登录系统，请稍候～', ['/'], 2);
            } else {
                $this->error($ucenterService->getInfo());
            }
        }
        
        // 获取当前需要执行的步骤
        $step = $userStep['step'] == UserIdentity::STEP_START ? $userStep['nextStep'] : $userStep['step'];
        $model = $this->_getStepModel($step, $uid, $identityId, $userStep);
        // 模型没有数据则进入下一个步骤
        if ($model === null) {
            return $this->controller->redirect('step');
        }
        $showSkipBtn = $userIdentityModel->checkStepCanSkip($step);
        $request = Yii::$app->getRequest();
        if ($request->getIsPost()) {
            $skip = $request->getBodyParam('skip', 0);
            $canSkip = $skip && $showSkipBtn;
            if ($model->save($request->getBodyParams(), $canSkip)) {
                if ($userStep['nextStep'] == UserIdentity::STEP_FINISHED) {
                    $activeUserConfig = Wc::$service->getSystem()->getConfig()->get('ACTIVE_USER');
                    // 完成流程步骤则激活当前用户
                    if ($activeUserConfig && in_array(1, explode(',', $activeUserConfig))) {
                        $this->_user = $ucenterService->getUser($identity);
                        $this->_user->updateAttributes(['is_active' => 1]);
                    }
                    goto quickLogin;
                } else {
                    return $this->controller->redirect('step');
                }
            } else {
                $this->error($model->message);
            }
        } else {
            return $this->display($this->_getStepView($step), [
                'model' => $model,
                'canSkip' => $showSkipBtn,
                'isFinished' => $userStep['nextStep'] == UserIdentity::STEP_FINISHED,
            ]);
        }
    }
    
    /**
     * 获取当前注册流程的视图文件名
     *
     * @param string $step 当前已执行到的步骤
     *
     * @return string
     */
    private function _getStepView($step)
    {
        switch ($step) {
            case UserIdentity::STEP_AVATAR:
                return 'flow-avatar';
            case UserIdentity::STEP_TAG:
                return 'flow-tag';
            case UserIdentity::STEP_PROFILE:
                return 'flow-profile';
            default:
                return '';
        }
    }
    
    /**
     * 获取当前注册流程的模型类
     *
     * @param string $step
     * @param integer $uid
     * @param integer $identityId
     * @param array $userStep
     *
     * @return FlowAvatarForm|FlowProfileForm|FlowTagForm
     * @throws NotFoundHttpException
     */
    private function _getStepModel($step, $uid, $identityId, $userStep)
    {
        $config = [
            'uid' => $uid,
            'identityId' => $identityId,
            'userStep' => $userStep,
        ];
        switch ($step) {
            case UserIdentity::STEP_AVATAR:
                $model = new FlowAvatarForm($config);
                break;
            case UserIdentity::STEP_TAG:
                $model = new FlowTagForm($config);
                break;
            case UserIdentity::STEP_PROFILE:
                $model = new FlowProfileForm($config);
                break;
            default:
                throw new NotFoundHttpException();
        }
        
        return $model;
    }
    
}