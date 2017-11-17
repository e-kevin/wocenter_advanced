<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'bootstrap',
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        'wocenter' => [
            'class' => 'wocenter\console\controllers\wocenter\controllers\WocenterController',
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'bootstrap' => [
            'method' => [
                'loadExtensionAliases',
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
        'menuService' => [
            'class' => 'wocenter\backend\modules\menu\services\MenuService',
        ],
    ],
    'params' => $params,
];
