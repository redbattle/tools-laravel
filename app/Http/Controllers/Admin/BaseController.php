<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Func;
use App\Models\AUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Exception;

class BaseController extends Controller
{
    protected $getUInfo = null;

    public function __construct(Request $request)
    {
        // 登录凭证
        $encrypt_token = $request->header('Access-Token');
        if (empty($encrypt_token)) {
            exit(self::jsonErr('登录已过期，请重新登录0', config('err_code')[4001]));
        }
        try {
            $token_date = decrypt($encrypt_token);
        } catch (Exception $exception) {
            exit(self::jsonErr('登录已过期，请重新登录1', config('err_code')[4001]));
        }
        $uid = $token_date['uid'];
        $token = $token_date['_token'];
        $res_cache = Func::cacheApiToken('a_user_' . $uid);
        $user = null;
        if ($res_cache) {
            if ($res_cache != $token) {
                $user = AUser::select(['id', 'username', 'nickname', 'status'])->where(['id' => $uid, 'session_key' => $token])->first();
                if ($user) {
                    Func::cacheApiToken('a_user_' . $uid, $token);
                } else {
                    exit(self::jsonErr('登录已过期，请重新登录1', config('err_code')[4001]));
                }
            }
        } else {
            $user = AUser::select(['id', 'username', 'nickname', 'status'])->where(['id' => $uid, 'session_key' => $token])->first();
            if ($user) {
                Func::cacheApiToken('a_user_' . $uid, $token);
            } else {
                exit(self::jsonErr('登录已过期，请重新登录2', config('err_code')[4001]));
            }
        }
        if ($user) {
            $this->getUInfo = $user;
        } else {
            $this->getUInfo = AUser::select(['id', 'username', 'nickname', 'status'])->find($uid);
        }
        if (empty($this->getUInfo) || $this->getUInfo->status < 0) {
            exit(self::jsonErr('登录已过期，请重新登录3', config('err_code')[4001]));
        }
    }
}
