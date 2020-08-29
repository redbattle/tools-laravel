<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 返回状态
    public static function ok($data = [], $msg = '成功')
    {
        return ['code' => 200, 'msg' => $msg, 'data' => (object)$data];
    }

    // 返回状态
    public static function err($msg = '失败', $err_code = '')
    {
        return ['code' => 0, 'msg' => $msg, 'err_code' => $err_code];
    }

    // 成功返回json状态
    public static function jsonOk($data = [], $msg = '成功')
    {
        return json_encode(['code' => 200, 'msg' => $msg, 'data' => (object)$data]);
    }

    // 失败返回json状态
    public static function jsonErr($msg = '失败', $err_code = '')
    {
        return json_encode(['code' => 0, 'msg' => $msg, 'err_code' => $err_code]);
    }

}
