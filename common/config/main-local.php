<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=47.99.46.219;port=3306;dbname=erp-dev',
            'username' => 'moxiaoheng',
            'password' => 'moxiaoheng123',
            'charset' => 'utf8',
            'tablePrefix' => 'bn_'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],

];
