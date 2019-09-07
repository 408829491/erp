<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'homeUrl' =>'/app',
    'basePath' => dirname(__DIR__),
    'language' => 'zh-CN',
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        "v1" => [
            'class' => 'api\modules\v1\Module',
        ],
        "v2" => [
            'class' => 'api\modules\v2\Module',
        ],
        "v3" => [
            'class' => 'api\modules\v3\Module',
        ],
    ],
    'aliases' => [
        'abei2017/wx'   => '@app/ext/yii2-wx/src',
    ],
    'components' => [
        'user' => [
			'identityClass' => 'common\models\User',
			'enableAutoLogin' => true,
			'enableSession' => true,
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
        'request' => [
            'cookieValidationKey' => 'P0r2XoT9LCUnyVlSgxBbJOqQxdCJ3i29',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'baseUrl' => '/app',
        ],
		'urlManager' => [
			'enablePrettyUrl' => true,
			'showScriptName' => false,
			'enableStrictParsing' => false,
			'rules' => [
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => ['v1/article'],
					'pluralize'=>false,
				],
				[
					'class' => 'yii\rest\UrlRule',
					'controller' => ['v1/user'],
					'pluralize'=>false,
                    'extraPatterns' => [
                        'GET login' => 'login',
                    ]
				],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/commodity'],
                    'pluralize'=>false
                ]
			]
		],
    ],
    'params' => $params,
];
