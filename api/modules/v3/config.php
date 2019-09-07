<?php
return [
    'components' => [
        'user' => [
            'identityClass' => 'common\models\UserDelivery',
            'enableAutoLogin' => true,
            'enableSession' => true,
        ],
        "authManager" => [
            "class" => 'yii\rbac\DbManager',
            "defaultRoles" => ["guest"],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v3/cusDelivery'],
                    'pluralize'=>false,
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v3/user'],
                    'pluralize'=>false,
                    'extraPatterns' => [
                        'GET login' => 'login',
                    ]
                ]
            ]
        ],
    ],
];
