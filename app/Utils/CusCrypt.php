<?php

namespace App\Utils;

class CusCrypt
{
    private static $method = 'aes-128-ecb';

    /**
     * 加密
     * @param mixed $data
     * @return string
     */
    public static function encrypt($data)
    {
        $encrypted = openssl_encrypt(json_encode($data), self::$method, config('auth.token_secret'), 0);
        return base64_encode($encrypted);
    }

    /**
     * 解密
     * @param string $sign
     * @return mixed
     */
    public static function decrypt(string $sign)
    {
        $decrypted = openssl_decrypt(base64_decode($sign), self::$method, config('wp.session_token'),0);
        return json_decode($decrypted);
    }

}
