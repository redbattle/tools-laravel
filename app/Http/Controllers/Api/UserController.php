<?php

namespace App\Http\Controllers\Api;

use App\Utils\CusFun;
use App\Models\CAccount;
use App\Models\CUser;
use App\Validators\Api\UserValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function logout(Request $request)
    {
        $user = $this->getUInfo;
        // 清除session_key
        $res = CUser::find($user->id)->update(['session_key' => '']);
        if ($res) {
            // 清除api token缓存
            CusFun::cacheApiToken('c_user_' . $user->id, null);
            return self::ok();
        } else {
            return self::err();
        }
    }

    /**
     * 获取信息
     * - nickname
     * - avatar
     * - phone
     * - email
     * @param Request $request
     * @return array
     */
    public function getInfo(Request $request)
    {
        $user = $this->getUInfo;
        return self::ok([
            'getInfo' => $user,
        ]);
    }

    /**
     * 更新基础信息
     * - nickname
     * @param Request $request
     * @return array
     */
    public function updateInfo(Request $request)
    {
        $user = $this->getUInfo;
        // 检查字段
        $validator = UserValidator::update_info($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_nickname = $request->input('nickname');
        $res = CUser::where(['id' => $user->id])->update(['nickname' => $r_nickname]);
        if ($res) {
            return self::ok();
        } else {
            return self::err();
        }
    }

    /**
     * 修改手机号
     * - phone
     * - password
     * - vcode
     * @param Request $request
     * @return array
     */
    public function updatePhone(Request $request)
    {
        $user = $this->getUInfo;
        // 检查字段
        $validator = UserValidator::update_phone($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_username = $request->input('phone');
        $r_password = $request->input('password');
        $r_vcode = $request->input('vcode');
        // 校验手机号
        if ($r_username == $user->phone) {
            return self::err('手机号不能与原手机号一致');
        }
        if (CAccount::isExist(['username' => $r_username], $user->uid)) {
            return self::err('手机号已注册');
        }
        // 校验密码
        $res = CUser::where(['id' => $user->id])->first();
        if (!Hash::check(md5($r_password), $res->password)) {
            return self::err('密码不正确');
        }
        // 校验验证码
        if (!CusFun::checkVCode($r_username, $r_vcode)) {
            return self::err('验证码不正确');
        }
        $res = CAccount::where(['uid' => $user->id, 'mode' => 'phone'])->update([
            'username' => $r_username,
        ]);
        if ($res) {
            CusFun::cacheVCode($r_username, null);
            return self::ok();
        } else {
            return self::err();
        }
    }

    /**
     * 绑定邮箱
     * - email
     * - vcode
     * @param Request $request
     * @return array
     */
    public function bindEmail(Request $request)
    {
        $user = $this->getUInfo;
        // 检查字段
        $validator = UserValidator::bind_email($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_username = $request->input('email');
        $r_vcode = $request->input('vcode');
        // 校验邮箱
        if ($user->email) {
            return self::err('账号已绑定邮箱');
        }
        if (CAccount::isExist(['username' => $r_username], $user->id)) {
            return self::err('邮箱已被其他账号绑定');
        }
        // 校验验证码
        if (!CusFun::checkVCode($r_username, $r_vcode)) {
            return self::err('验证码不正确');
        }
        $res = CAccount::create([
            'uid' => $user->id,
            'username' => $r_username,
            'mode' => 'email',
        ]);
        if ($res) {
            CusFun::cacheVCode($r_username, null);
            return self::ok();
        } else {
            return self::err();
        }
    }

    /**
     * 修改邮箱
     * - email
     * - password
     * - vcode
     * @param Request $request
     * @return array
     */
    public function updateEmail(Request $request)
    {
        $user = $this->getUInfo;
        // 检查字段
        $validator = UserValidator::update_email($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_username = $request->input('email');
        $r_password = $request->input('password');
        $r_vcode = $request->input('vcode');
        // 校验手机号
        if ($r_username == $user->email) {
            return self::err('邮箱不能与原邮箱一致');
        }
        if (CAccount::isExist(['username' => $r_username], $user->uid)) {
            return self::err('邮箱已被其他账号绑定');
        }
        // 校验密码
        $res = CUser::where(['id' => $user->id])->first();
        if (!Hash::check(md5($r_password), $res->password)) {
            return self::err('密码不正确');
        }
        // 校验验证码
        if (!CusFun::checkVCode($r_username, $r_vcode)) {
            return self::err('验证码不正确');
        }
        $res = CAccount::where(['uid' => $user->id, 'mode' => 'email'])->update([
            'username' => $r_username,
        ]);
        if ($res) {
            CusFun::cacheVCode($r_username, null);
            return self::ok();
        } else {
            return self::err();
        }
    }

    /**
     * 修改密码
     * - password_old
     * - password
     * - password_confirmation
     * @param Request $request
     * @return array
     */
    public function updatePassword(Request $request)
    {
        $user = $this->getUInfo;
        // 检查字段
        $validator = UserValidator::update_password($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_password_old = $request->input('password_old');
        $r_password = $request->input('password');
        // 校验密码
        $result = CUser::where(['id' => $user->id])->first();
        if (!Hash::check(md5($r_password_old), $result->password)) {
            return self::err('旧密码不正确');
        }
        $res = $result->update([
            'password' => bcrypt(md5($r_password)),
        ]);
        if ($res) {
            return self::ok();
        } else {
            return self::err();
        }
    }

}
