<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'frontend',
    'name' => 'WoCenter Advanced',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => [
        'log',
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
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
        ],
        'extensionService' => [
            'class' => 'wocenter\backend\modules\extension\services\ExtensionService',
            'subService' => [
                'controller' => ['class' => 'wocenter\backend\modules\extension\services\sub\ControllerService'],
                'modularity' => ['class' => 'wocenter\backend\modules\extension\services\sub\ModularityService'],
                'load' => ['class' => 'wocenter\backend\modules\extension\services\sub\LoadService'],
                'theme' => [
                    'class' => 'wocenter\backend\modules\extension\services\sub\ThemeService',
                    'themeConfigKey' => 'FRONTEND_THEME',
                    'defaultTheme' => 'yii2-frontend-theme-basic',
                ],
            ],
        ],
        'systemService' => [
            'class' => 'wocenter\backend\modules\system\services\SystemService',
            'subService' => [
                'config' => ['class' => 'wocenter\backend\modules\system\services\sub\ConfigService'],
            ],
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
