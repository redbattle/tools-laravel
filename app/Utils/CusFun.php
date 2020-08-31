<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Overtrue\EasySms\EasySms;

class CusFun
{
    protected static function cacheData($save_key, $value, $minutes)
    {
        if ($value === '') {
            return Cache::get($save_key);
        } else if (is_null($value)) {
            Cache::forget($save_key);
            $save = Cache::get($save_key);
            if (is_null($save)) {
                return true;
            } else {
                return false;
            }
        } else {
            Cache::put($save_key, $value, $minutes);
            $save = Cache::get($save_key);
            if ($save == $value) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 验证用户名格式
     * @param $username
     * @return false|int
     */
    public static function checkUsername($username)
    {
        return preg_match('/^\w{4,20}$/', $username);
    }

    /**
     * 验证邮箱格式
     * @param $email
     * @return false|int
     */
    public static function checkEmail($email)
    {
        return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email);
    }

    /**
     * 验证中国大陆手机号格式
     * @param $phone
     * @return false|int
     */
    public static function checkPhone($phone)
    {
        return preg_match('/^1[3456789]\d{9}$/', $phone);
    }

    /**
     * 随机数
     * @param int $len
     * @param string $format
     * @return string
     */
    public static function code($len = 4, $format = 'all')
    {
        switch (strtoupper($format)) {
            case 'CHAR':
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz123456789';
                break;
        }
        $res = '';
        for ($i = 0; $i < $len; $i++) {
            $j = mt_rand(0, strlen($chars) - 1);
            $res .= substr($chars, $j, 1);
        }
        return $res;
    }

    /**
     * 缓存token
     * @param $key
     * @param string $value
     * @return bool
     */
    public static function cacheApiToken($key, $value = '')
    {
        $save_key = md5(config('cus_dict.cache.c_token.prefix') . $key);
        $minutes = config('cus_dict.cache.c_token.exp');
        return self::cacheData($save_key, $value, $minutes);
    }

    /**
     * 缓存验证码
     * @param $key
     * @param string $value
     * @return bool
     */
    public static function cacheVCode($key, $value = '')
    {
        $save_key = md5(config('cus_dict.cache.vCode.prefix') . $key);
        $minutes = config('cus_dict.cache.vCode.exp');
        return self::cacheData($save_key, $value, $minutes);
    }

    /**
     * 系统内token
     * @param $key
     * @return string
     */
    public static function getToken($key)
    {
        return md5(encrypt(config('auth.token_secret') . $key));
    }

    /**
     * 校验验证码
     * @param $key
     * @param $value
     * @return bool
     */
    public static function checkVCode($key, $value)
    {
        if (in_array(config('app.env'), ['testing']) || (!empty($value) && self::cacheVCode($key) == $value)) {
            return true;
        } else {
            return false;
        }
    }

    // 发送手机验证码
    protected static function sendPhoneVCode($phone, $is_send = true)
    {
        if ($is_send) {
            $code = self::code(4, 'NUMBER');
        } else {
            $code = config('cus_dict.verify_code.default');
        }
        $cache = self::cacheVCode($phone, $code);
        if ($cache) {
            if ($is_send) {
                $config = config('sms');
                $easySms = new EasySms($config);
                $res = $easySms->send($phone, [
                    'template' => $config['template_id'],
                    'data' => [
                        'code' => $code
                    ],
                ]);
                if ($res['aliyun']['status'] == 'success') {
                    return true;
                }
            } else {
                return true;
            }
        }
        return '验证码发送失败';
    }

    // 发送邮箱验证码
    protected static function sendEmailVCode($email, $is_send = true)
    {
        if ($is_send) {
            $code = self::code(4, 'NUMBER');
        } else {
            $code = config('cus_dict.verify_code.default');
        }
        $cache = self::cacheVCode($email, $code);
        if ($cache) {
            if ($is_send) {
                $data = ['email' => $email, 'VCode' => $code];
                Mail::send('template.emailVCode', $data, function ($message) use ($data) {
                    $message->to($data['email'])->subject('[CherryPlan]验证码');
                });
            }
            return true;
        } else {
            return '验证码发送失败';
        }
    }

    /**
     * 发送验证码
     * @param string $username 手机号或邮箱
     * @param object $Model Model
     * @param int $is_exist 验证是否存在；-1不存在才发送，1存在才发送，0不验证
     * @return bool|string
     */
    public static function sendVCode($username, $Model, $is_exist = 0)
    {
        if ($is_exist != 0) {
            // account model
            $m = $Model::where(['username' => $username])->first();
            if ($m && $m->cUser) {
                // 已有账号
                if ($m->cUser->status == 0) {
                    return '账号已被禁用';
                }
                if ($is_exist == -1) {
                    return '账号已存在';
                }
            } else {
                if ($is_exist == 1) {
                    return '账号不存在';
                }
            }
        }
        $is_send = !in_array(config('app.env'), config('cus_dict.verify_code.no_send_env'));
        if (self::checkEmail($username)) {
            // 邮箱
            return self::sendEmailVCode($username, $is_send);
        } else if (self::checkPhone($username)) {
            // 手机号
            return self::sendPhoneVCode($username, $is_send);
        } else {
            // 格式错误
            return '账号格式错误';
        }
    }


    /**
     * 文件大小格式化
     * @param $bytes
     * @return string
     */
    public static function fileSizeFormat($bytes)
    {
        $bytes *= 1;
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return number_format($bytes, 2, '.', '') . ' ' . $units[$i];
    }
}
