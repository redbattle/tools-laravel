<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel sms
    |--------------------------------------------------------------------------
    |
    | 短信服务
    |
    */

    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [
            'aliyun',
        ],
    ],
    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => storage_path('logs/easy-sms.log'),
        ],
        'aliyun' => [
            'access_key_id' => env('SMS_ACCESS_KEY_ID'),
            'access_key_secret' => env('SMS_ACCESS_KEY_SECRET'),
            'sign_name' => env('SMS_ACCESS_KEY_SIGN_NAME'),
        ],
    ],

    // 模板id
    'template_id' => env('SMS_ACCESS_KEY_TEMPLATE_ID'),

];
