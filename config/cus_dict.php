<?php

return [

    /*
    |--------------------------------------------------------------------------
    | cus_dict
    |--------------------------------------------------------------------------
    */

    // 每页显示条数
    'pageSize' => 20,

    // 状态
    'status' => [
        // 默认
        'default' => [
            [
                'key' => 0,
                'status' => 'error',
                'text' => '禁用',
            ],
            [
                'key' => 1,
                'status' => 'success',
                'text' => '正常',
            ],
        ],

    ],

    // 是否
    'yesOrNo' => [
        [
            'key' => 0,
            'status' => 'error',
            'text' => '否',
        ],
        [
            'key' => 1,
            'status' => 'success',
            'text' => '是',
        ],
    ],

    // 缓存 有效期为秒数
    'cache' => [
        // 验证码
        'vCode' => [
            'exp' => 600, // 10分钟
            'prefix' => 'cache_vcode_',
        ],
        // c user token
        'c_token' => [
            'exp' => 7200, // 2小时
            'prefix' => 'cache_c_token_',
        ],
    ],

    // 验证码免发送模式
    'verify_code' => [
        'no_send_env' => ['testing', 'local'],
        'default' => '1234',
    ],

    // file
    'file' => [
        'image' => [
            'size' => 1, // M
        ],
        'app' => [
            'size' => 40, // M
        ],
    ],


];
