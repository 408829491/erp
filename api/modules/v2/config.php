<?php
return [
    'components' => [
        'user' => [
            'identityClass' => 'common\models\UserCus',
            'enableAutoLogin' => true,
            'enableSession' => true,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v2/cusStore'],
                    'pluralize'=>false,
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v2/user'],
                    'pluralize'=>false,
                    'extraPatterns' => [
                        'GET login' => 'login',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v2/commodity'],
                    'pluralize'=>false
                ]
            ]
        ],
    ],
];
