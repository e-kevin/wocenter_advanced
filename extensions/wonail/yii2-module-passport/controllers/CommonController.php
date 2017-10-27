<?php

namespace wocenter\backend\modules\passport\controllers;

use wocenter\backend\modules\passport\models\LoginForm;
use wocenter\core\Controller;
use wocenter\Wc;
use Yii;
use yii\filters\AccessControl;
use yii\filters\PageCache;
use yii\filters\VerbFilter;

/**
 * 通行证公用控制器
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class CommonController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'signup', 'invite-signup', 'step', 'logout-on-step'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'logout-on-step' => ['post'],
                ],
            ],
//            'pageCache' => [
//                'class' => PageCache::className(),
//                'only' => ['login', 'signup'],
//                'duration' => 60,
//            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            'login',
            'signup',
            'invite-signup',
            'step',
        ];
    }
    
    /**
     * 退出登陆
     *
     * @return mixed|string
     */
    public function actionLogout()
    {
        Wc::$service->getPassport()->getUcenter()->logout();
        
        return $this->success('退出成功', Yii::$app->getUser()->loginUrl);
    }
    
    /**
     * 注册流程里退出登陆
     *
     * @return mixed|string
     */
    public function actionLogoutOnStep()
    {
        // 删除SESSION
        Yii::$app->getSession()->remove(LoginForm::TMP_LOGIN);
        
        return $this->redirect(Yii::$app->getUser()->loginUrl);
    }
    
}
