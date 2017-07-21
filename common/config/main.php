<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\ApcCache',
            'keyPrefix' => 'wc_', // 唯一键前缀
        ],
        'schemaCache' => [
            'class' => 'yii\caching\FileCache',
            'keyPrefix' => 'scheme_', //统一缓存目录管理缓存文件,如果注释此设置,缓存文件则会自动分配至不同文件夹里
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'security' => [
            'passwordHashStrategy' => 'password_hash', // 生成密码的方法，必须PHP版本>=5.5.0，否则注释该行
        ],
        'assetManager' => [
            'linkAssets' => true,
        ],
    ],
    'timeZone' => 'PRC',
];
