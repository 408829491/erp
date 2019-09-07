<?php
return [
    'adminEmail' => 'admin@example.com',
    'wx' => [
        //  公众号信息
        'mp' => [
            /**
             * 账号基本信息，请从微信公众平台
             */
            'app_id' => 'wx490f1d21e4c7765c',         // AppID
            'secret' => 'c37a92b476d5b7d8f38087076000466f',     // AppSecret
            'token' => 'a2VWv9xsyz2v2JZWt8I459Z9gS2fWf8l',          // Token
            'encodingAESKey' => 'loSIOzWWofewrIkeeIIIePOkkfOwqwoLOfSFBwOE8zF',// 消息加解密密钥,该选项需要和公众号后台设置保持一直
            'safeMode' => 0,//0-明文 1-兼容 2-安全，该选项需要和公众号后台设置保持一直
            'payment' => [
                'mch_id' => '1484145142',
                'key' => '75PUcjS1z0Lu6dAATxQZkloEXOitpCCq',
                'notify_url' => 'https://app.moxiaoheng.com/app/v1/we-chat/notify',
                'cert_path' => '', // XXX: 绝对路径
                'key_path' => '',      // XXX: 绝对路径
            ],
            'oauth' => [
                'scopes' => 'snsapi_userinfo',
                'callback' => '',
            ],
        ],
        'mini' => [
            'app_id' => 'wx96ac1056d9cce706',
            'secret' => '162b7489eb9effe01d97e2494c2d1e8b',
            'payment' => [
                'mch_id' => '1484145142',
                'key' => '75PUcjS1z0Lu6dAATxQZkloEXOitpCCq'
            ],
        ],
        'mini2' => [
            'app_id' => 'wx4c7cc700cbdf3e07',
            'secret' => '34cdc9f7754351d1fe1aeba1c872f4de',
            'payment' => [
                'mch_id' => '1484145142',
                'key' => '75PUcjS1z0Lu6dAATxQZkloEXOitpCCq'
            ],
        ],
    ],
];
