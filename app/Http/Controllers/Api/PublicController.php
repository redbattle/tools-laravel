<?php

namespace App\Http\Controllers\Api;

use App\Utils\CusFun;
use App\Validators\Api\PublicValidator;
use App\Models\CAccount;
use App\Models\CUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PublicController extends Controller
{

    // 获取验证码
    public function getVCode(Request $request)
    {
        // 检查字段
        $validator = PublicValidator::getVCode($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_username = $request->post('username');
        $r_is_exist = $request->post('is_exist');
        $res = CusFun::sendVCode($r_username, CAccount::class, $r_is_exist);
        if ($res === true) {
            return self::ok();
        } else {
            return self::err($res);
        }
    }

    // 注册
    public function register(Request $request)
    {
        // 检查字段
        $validator = PublicValidator::register($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_nickname = $request->post('nickname');
        $r_phone = $request->post('phone');
        $r_password = $request->post('password');
        $r_vcode = $request->post('vcode');
        // 验证有效性
        if (!CusFun::checkVCode($r_phone, $r_vcode)) {
            return self::err('验证码不正确');
        }
        $session_key = CusFun::getToken($r_phone);
        DB::beginTransaction();
        // 保存数据
        $res = CUser::create([
            'nickname' => $r_nickname,
            'password' => bcrypt(md5($r_password)),
            'session_key' => $session_key,
            'status' => 1,
        ]);
        if ($res) {
            $res_account = CAccount::create([
                'uid' => $res->id,
                'username' => $r_phone,
                'mode' => 'phone',
            ]);
            if ($res_account) {
                DB::commit();
                CusFun::cacheApiToken('c_user_' . $res->id, $session_key);
                CusFun::cacheVCode($r_phone, null);
                $encrypt_token = encrypt([
                    'uid' => $res->id,
                    '_token' => $session_key,
                ]);
                return self::ok([
                    '_token' => $encrypt_token,
                ]);
            } else {
                DB::rollBack();
                return self::err('注册失败');
            }
        } else {
            DB::rollBack();
            return self::err('注册失败');
        }
    }

    // 登录
    public function login(Request $request)
    {
        // 检查字段
        $validator = PublicValidator::login($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_mode = $request->post('mode');
        $r_username = $request->post('username');
        $r_password = $request->post('password');
        $r_vcode = $request->post('vcode');
        // 查询用户信息
        $ca = CAccount::where(['username' => $r_username])->first();
        if (!$ca || !$ca->cUser) {
            return self::err('账号不存在');
        } else if ($ca->cUser->status == 0) {
            return self::err('账号被禁用');
        }
        // 验证登录
        if ($r_mode === 'vcode') {
            // 验证码登录
            if (!CusFun::checkVCode($r_username, $r_vcode)) {
                return self::err('验证码不正确');
            }
        } else if ($r_mode === 'password') {
            // 密码登录
            if (!Hash::check(md5($r_password), $ca->cUser->password)) {
                return self::err('账号或密码错误');
            }
        }
        $session_key = CusFun::getToken($ca->id);
        $encrypt_token = encrypt([
            'uid' => $ca->cUser->id,
            '_token' => $session_key,
        ]);
        $res = $ca->cUser->update([
            'session_key' => $session_key,
        ]);
        if ($res) {
            CusFun::cacheApiToken('c_user_' . $ca->cUser->id, $session_key);
            if ($r_mode === 'vcode') {
                CusFun::cacheVCode($r_username, null);
            }
            return self::ok([
                '_token' => $encrypt_token,
            ]);
        } else {
            return self::err('登录失败');
        }
    }

    // 重置密码
    public function resetPassword(Request $request)
    {
        // 检查字段
        $validator = PublicValidator::resetPassword($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_username = $request->post('username');
        $r_vcode = $request->post('vcode');
        $r_password = $request->post('password');
        // 验证账号信息
        $account = CAccount::where(['username' => $r_username])->first();
        if (empty($account)) {
            return self::err('账号不存在');
        }
        // 验证有效性
        if (!CusFun::checkVCode($r_username, $r_vcode)) {
            return self::err('验证码不正确');
        }
        // 重置密码
        $res = CUser::where(['id' => $account->uid])->update([
            'password' => bcrypt(md5($r_password)),
            'session_key' => '',
        ]);
        if ($res) {
            CusFun::cacheVCode($r_username, null);
            CusFun::cacheApiToken('c_user_' . $account->uid, null);
            return self::ok();
        } else {
            return self::err();
        }
    }

}
