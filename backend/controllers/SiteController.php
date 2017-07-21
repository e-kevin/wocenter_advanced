<?php
namespace backend\controllers;

use wocenter\core\Controller;

/**
 * 首页控制器
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->display();
    }

    public function actionError()
    {
        return $this->runDispatch();
    }

}
