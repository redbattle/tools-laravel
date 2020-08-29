<?php

namespace App\Validators\Admin;


use Illuminate\Support\Facades\Validator;

class PublicValidator
{
    /**
     * admin user login validator
     * @param $request
     * @return bool|mixed
     */
    public static function login($request)
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];
        $msg = [
            'username.required' => '用户名不能为空',
            'phone.required' => '密码不能为空',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

}
