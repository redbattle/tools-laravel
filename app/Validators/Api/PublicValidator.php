<?php

namespace App\Validators\Api;


use App\Rules\Password;
use App\Rules\Phone;
use Illuminate\Support\Facades\Validator;

class PublicValidator
{
    /**
     * app user getVCode validator
     * @param $request
     * @return bool|mixed
     */
    public static function getVCode($request)
    {
        $rules = [
            'username' => 'required',
            'is_exist' => 'sometimes|in:0,1,-1',
        ];
        $msg = [
            'username.required' => '账号不能为空',
            'is_exist.in' => '参数值无效',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user register validator
     * @param $request
     * @return bool|mixed
     */
    public static function register($request)
    {
        $rules = [
            'nickname' => 'required|max:30',
            'phone' => ['required', new Phone(), 'unique:c_accounts,username'],
            'password' => ['required', new Password()],
            'vcode' => 'required',
        ];
        $msg = [
            'nickname.required' => '昵称不能为空',
            'nickname.max' => '昵称不能超过30字符',
            'phone.required' => '手机号不能为空',
            'phone.unique' => '手机号已注册',
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
     * app user login validator
     * @param $request
     * @return bool|mixed
     */
    public static function login($request)
    {
        $rules = [
            'mode' => 'in:vcode,password',
            'username' => 'required',
            'vcode' => 'required_if:mode,vcode',
            'password' => 'required_if:mode,password'
        ];
        $msg = [
            'mode.in' => '登录方式不正确',
            'username.required' => '账号不能为空',
            'password.required_if' => '密码不能为空',
            'vcode.required_if' => '验证码不能为空',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user getVersion validator
     * @param $request
     * @return bool|mixed
     */
    public static function getVersion($request)
    {
        $rules = [
            'client' => 'required|in:ios,android',
        ];
        $msg = [
            'client.required' => '客户端不能为空',
            'client.in' => '客户端值必须为iOS或Android',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

    /**
     * app user resetPassword validator
     * @param $request
     * @return bool|mixed
     */
    public static function resetPassword($request)
    {
        $rules = [
            'username' => 'required',
            'vcode' => 'required',
            'password' => ['required', new Password(), 'confirmed'],
        ];
        $msg = [
            'username.required' => '账号不能为空',
            'vcode.required' => '验证码不能为空',
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
