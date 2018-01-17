<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'backend',
    'name' => 'WoCenter System',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => [
        'log',
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'view' => [
            'class' => '\wocenter\core\View',
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
        ],
        'extensionService' => [
            'class' => 'wocenter\backend\modules\extension\services\ExtensionService',
            'subService' => [
                'controller' => ['class' => 'wocenter\backend\modules\extension\services\sub\ControllerService'],
                'modularity' => ['class' => 'wocenter\backend\modules\extension\services\sub\ModularityService'],
                'load' => ['class' => 'wocenter\backend\modules\extension\services\sub\LoadService'],
                'theme' => ['class' => 'wocenter\backend\modules\extension\services\sub\ThemeService'],
                'dependent' => ['class' => 'wocenter\backend\modules\extension\services\sub\DependentService'],
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
