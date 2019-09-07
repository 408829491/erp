<?php
return [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'jc4RY1oIef9Mbk2Ae_R6-eZyRdw1frHV',
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
        ]
    ]
];
