<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
		//文件缓存组件
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
		//memcache缓存组件 我们的框架用到了memcache缓存哦，所以需要配置这个
		'memcache' => [  
			'class' => 'yii\caching\MemCache',  
			'useMemcached' =>0, //这里简单说明一下 0是memcache, 1是memcached 两个是php里不同的扩展
			'servers' => [  
				[  
					'host' => '127.0.0.1',  
					'port' => 11211,  
					'weight' => 100,  
				]
			],  
		],
        'Aliyunoss' => [
            'class' => 'common\components\Aliyunoss',
        ],
        'aliyun' => [
            'class' => 'saviorlv\aliyun\Sms',
            'accessKeyId' => 'LTAI5dWyZ1EYmHUc',
            'accessKeySecret' => 'hebhS0PV4AKCJIj0AvP7cRx1KI7mSx'
        ],
    ],
];
