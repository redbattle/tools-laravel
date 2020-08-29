<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Func;
use App\Models\AUser;
use App\Validators\Admin\PublicValidator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PublicController extends Controller
{

    // 登录
    public function login(Request $request)
    {
        // 检查字段
        $validator = PublicValidator::login($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $username = $request->post('username');
        $password = $request->post('password');
        $user = AUser::where(['username' => $username])->first();
        if (!$user) {
            return self::err('用户名不存在');
        } else if ($user->status == 0) {
            return self::err('用户被禁用');
        } else if (!Hash::check($password, $user->password)) {
            return self::err('用户名或密码错误');
        }
        $session_key = Func::getToken($user->id);
        $encrypt_token = encrypt([
            'uid' => $user->id,
            '_token' => $session_key,
        ]);
        $res = $user->update([
            'session_key' => $session_key,
        ]);
        if ($res) {
            Func::cacheApiToken('a_user_' . $user->id, $session_key);
            return self::ok([
                '_token' => $encrypt_token,
            ]);
        } else {
            return self::err('登录失败');
        }
    }

}
