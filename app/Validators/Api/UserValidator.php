<?php

namespace App\Validators\Api;


use App\Rules\Password;
use App\Rules\Phone;
use Illuminate\Support\Facades\Validator;

class UserValidator
{
    /**
     * app user update base info validator
     * @param $request
     * @return bool|mixed
     */
    public static function update_info($request)
    {
        $rules = [
            'nickname' => 'required|max:30',
        ];
        $msg = [
            'nickname.required' => '昵称不能为空',
            'nickname.max' => '昵称不能超过30字符',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user update phone validator
     * @param $request
     * @return bool|mixed
     */
    public static function update_phone($request)
    {
        $rules = [
            'phone' => ['required', new Phone()],
            'password' => 'required',
            'vcode' => 'required',
        ];
        $msg = [
            'phone.required' => '手机号不能为空',
            'password.required' => '密码不能为空',
            'vcode.required' => '验证码不能为空',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user bind email validator
     * @param $request
     * @return bool|mixed
     */
    public static function bind_email($request)
    {
        $rules = [
            'email' => 'required|email',
            'vcode' => 'required',
        ];
        $msg = [
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'vcode.required' => '验证码不能为空',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user update email validator
     * @param $request
     * @return bool|mixed
     */
    public static function update_email($request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
            'vcode' => 'required',
        ];
        $msg = [
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'password.required' => '密码不能为空',
            'vcode.required' => '验证码不能为空',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user update password validator
     * @param $request
     * @return bool|mixed
     */
    public static function update_password($request)
    {
        $rules = [
            'password_old' => 'required',
            'password' => ['required', new Password(), 'confirmed'],
        ];
        $msg = [
            'password_old.required' => '旧密码不能为空',
            'password.required' => '新密码不能为空',
            'password.confirmed' => '新密码与确认密码不一致',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

}
