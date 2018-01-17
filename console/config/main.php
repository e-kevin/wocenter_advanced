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
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
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
        'extensionService' => [
            'class' => 'wocenter\backend\modules\extension\services\ExtensionService',
            'subService' => [
                'controller' => ['class' => 'wocenter\backend\modules\extension\services\sub\ControllerService'],
                'modularity' => ['class' => 'wocenter\backend\modules\extension\services\sub\ModularityService'],
                'load' => ['class' => 'wocenter\backend\modules\extension\services\sub\LoadService'],
                'theme' => ['class' => 'wocenter\backend\modules\extension\services\sub\ThemeService'],
            ],
        ],
        'menuService' => [
            'class' => 'wocenter\backend\modules\menu\services\MenuService',
        ],
    ],
    'params' => $params,
];
