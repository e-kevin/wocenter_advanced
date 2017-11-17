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
    'id' => 'frontend',
    'name' => 'WoCenter System',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
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
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => '\wocenter\backend\modules\account\models\BaseUser',
            'loginUrl' => ['/passport/common/login'],
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
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
        'view' => [
            'class' => '\wocenter\core\View',
        ],
        'session' => [
            'name' => 'advanced-frontend',
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
            'showScriptName' => true,
//            'suffix' => '.html',
            'rules' => [
                '' => 'site/index',
            ],
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
        'systemService' => [
            'class' => 'wocenter\backend\modules\system\services\SystemService',
            'subService' => [
                'config' => ['class' => 'wocenter\backend\modules\system\services\system\ConfigService'],
            ],
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
