<?php

namespace App\Validators\Admin;


use App\Rules\Password;
use App\Rules\Username;
use Illuminate\Support\Facades\Validator;

class AUserValidator
{
    /**
     * admin a_user save validator
     * @param $request
     * @return bool|mixed
     */
    public static function save($request)
    {
        $rules = [
            'nickname' => 'required|max:30',
            'username' => ['required','max:50', new Username()],
            'password' => ['sometimes', new Password()],
            'status' => 'required|in:0,1',
        ];
        $msg = [
            'nickname.required' => '昵称不能为空',
            'nickname.max' => '昵称不能超过30字符',
            'username.required' => '用户名不能为空',
            'username.max' => '用户名不能超过30字符',
            'status.required' => '状态不能为空',
            'status.in' => '状态值无效',
        ];
        if (is_null($request->input('password'))){
            unset($request['password']);
        }
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }


    /**
     * admin user upload image validator
     * @param $request
     * @return bool|mixed
     */
    public static function uploadFile($request)
    {
        $rules = [
            'file' => 'required',
        ];
        $msg = [
            'file.required' => '文件不能为空',
        ];
        $error = Validator::make($request->all(), $rules, $msg)->errors()->first();
        if ($error) {
            return $error;
        }
        return true;
    }

}
