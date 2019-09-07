<?php
return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation'=>false,
            'cookieValidationKey' => 'LbxFBStHa4F6s_AuZ7IrlHq-1MYAZZWG',
        ],
    ],
    'bootstrap' =>['gii'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' =>['*'],
            'generators' =>[
                'crud' => [
                    'class' => 'yii\gii\generators\crud\Generator',
                     'templates' => [
                        'layuiCrud' => '@common/gii/crud',
                      ]
                ]
            ]
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
            'allowedIPs' => ['1.2.3.4', '127.0.0.1', '::1']
        ]
    ]
];
