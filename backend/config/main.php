<?php
$captchaAction = '/passport/security/captcha'; // 验证码路由地址
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
$params['captchaAction'] = $captchaAction;

return [
    'id' => 'backend',
    'name' => 'WoCenter System',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log',
        'bootstrap',
    ],
    'container' => [
        'definitions' => [
            'yii\captcha\Captcha' => [
                'captchaAction' => $captchaAction,
                'template' => '<div class="input-group">{input}<div class="input-group-addon">{image}</div></div>',
            ],
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => '\wocenter\backend\modules\account\models\User',
            'loginUrl' => ['/passport/common/login'],
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'class' => '\wocenter\core\View',
        ],
        'i18n' => [
            'translations' => [
                'wocenter/*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@wocenter/messages',
                ],
            ],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'suffix' => '.html',
            'rules' => [
                '' => 'site/index',
            ],
        ],
        'notificationService' => [
            'class' => 'wocenter\backend\modules\notification\services\NotificationService',
            'subService' => [
                'message' => ['class' => 'wocenter\backend\modules\notification\services\notification\MessageService'],
                'sms' => ['class' => 'wocenter\backend\modules\notification\services\notification\SmsService'],
                'email' => ['class' => 'wocenter\backend\modules\notification\services\notification\EmailService'],
            ],
        ],
        'logService' => [
            'class' => 'wocenter\backend\modules\log\services\LogService',
        ],
        'passportService' => [
            'class' => 'wocenter\backend\modules\passport\services\PassportService',
            'subService' => [
                'ucenter' => ['class' => 'wocenter\backend\modules\passport\services\passport\UcenterService'],
                'verify' => ['class' => 'wocenter\backend\modules\passport\services\passport\VerifyService'],
                'validation' => ['class' => 'wocenter\backend\modules\passport\services\passport\ValidationService'],
            ],
        ],
        'systemService' => [
            'class' => 'wocenter\backend\modules\system\services\SystemService',
            'subService' => [
                'config' => ['class' => 'wocenter\backend\modules\system\services\system\ConfigService'],
            ],
        ],
        'menuService' => [
            'class' => 'wocenter\backend\modules\menu\services\MenuService',
        ],
        'actionService' => [
            'class' => 'wocenter\backend\modules\action\services\ActionService',
        ],
        'accountService' => [
            'class' => 'wocenter\backend\modules\account\services\AccountService',
        ],
        'extensionService' => [
            'class' => 'wocenter\backend\modules\extension\services\ExtensionService',
            'subService' => [
                'controller' => ['class' => 'wocenter\backend\modules\extension\services\extension\ControllerService'],
                'modularity' => ['class' => 'wocenter\backend\modules\extension\services\extension\ModularityService'],
                'load' => ['class' => 'wocenter\backend\modules\extension\services\extension\LoadService'],
                'theme' => ['class' => 'wocenter\backend\modules\extension\services\extension\ThemeService'],
            ],
        ],
    ],
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ],
        'datecontrol' => [
            'class' => '\kartik\datecontrol\Module',
            'autoWidgetSettings' => [
                'date' => [
                    'pluginOptions' => [
                        'autoclose' => true,
                    ],
                ],
                'datetime' => [
                    'pluginOptions' => [
                        'showMeridian' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                    ],
                ],
                'time' => [
                    'pluginOptions' => [
                        'autoclose' => true,
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
