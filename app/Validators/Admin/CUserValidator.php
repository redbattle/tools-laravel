<?php

namespace App\Validators\Admin;


use App\Rules\Password;
use App\Rules\Phone;
use Illuminate\Support\Facades\Validator;

class CUserValidator
{
    /**
     * admin c_user update validator
     * @param $request
     * @return bool|mixed
     */
    public static function update($request)
    {
        $rules = [
            'id' => 'required',
            'nickname' => 'required|max:30',
            'password' => ['sometimes', new Password()],
            'status' => 'required|in:0,1',
            'phone' => ['required', new Phone()],
            'email' => 'sometimes|email',
        ];
        $msg = [
            'id.required' => 'id不能为空',
            'nickname.required' => '昵称不能为空',
            'nickname.max' => '昵称不能超过30字符',
            'phone.required' => '手机号不能为空',
            'status.required' => '状态不能为空',
            'status.in' => '状态值无效',
            'email.email' => '邮箱格式不正确',
        ];
        if (is_null($request->input('password'))) {
            unset($request['password']);
        }
        if (is_null($request->input('email'))) {
            unset($request['email']);
        }
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

}
