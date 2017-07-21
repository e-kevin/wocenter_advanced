<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wocenter',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'wc_',
            'enableSchemaCache' => false, // 开启模式缓存
            'schemaCacheDuration' => 3600, // 模式缓存持续时间
            'schemaCache' => 'schemaCache', // 使用的缓存组件名，缺省为 'cache'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.qq.com',
                'username' => 'xx@qq.com',
                'password' => '',
                'port' => '465', //25
                'encryption' => 'ssl', //tls
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
            ],
        ],
    ],
];
