<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Func;
use App\Models\AUser as TM;
use App\Validators\Admin\AUserValidator;
use Illuminate\Http\Request;

class AUserController extends BaseController
{
    public function getList(Request $request)
    {
        $r_username = $request->input('username');
        $r_status = $request->input('status');
        $request['page'] = intval($request->input('pageNo', '1'));
        $r_page_size = intval($request->input('pageSize', '10'));
        // 查询条件
        $where = [];
        if (!is_null($r_status)) {
            $where['status'] = $r_status;
        }
        if (!is_null($r_username)) {
            $where[] = ['username', 'like', '%' . $r_username . '%'];
        }
        $lists = TM::select([
            'id',
            'username',
            'nickname',
            'status'
        ])->where($where)->orderBy('id', 'desc')->paginate($r_page_size);
        return self::ok([
            'lists' => $lists,
            'status_lists' => config('params.status.default'),
        ]);
    }

    public function getInfo(Request $request)
    {
        $user = $this->getUInfo;
        return self::ok([
            'userInfo' => $user,
        ]);
    }

    public function save(Request $request)
    {
        // 检查字段
        $validator = AUserValidator::save($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_id = $request->input('id');
        $r_nickname = $request->input('nickname');
        $r_username = $request->input('username');
        $r_password = $request->input('password');
        $r_status = $request->input('status');
        // 字段限制
        if (TM::isExist(['username' => $r_username], $r_id)) {
            return self::err('用户名已存在');
        }
        $saveData = [
            'username' => $r_username,
            'nickname' => $r_nickname,
            'status' => $r_status,
        ];
        if (!is_null($r_password)) {
            $saveData['password'] = bcrypt(md5($r_password));
        }
        if ($r_id) {
            $res = TM::where(['id' => $r_id])->update($saveData);
        } else {
            if (is_null($r_password)) {
                return self::err('密码不能为空');
            }
            $res = TM::create($saveData);
        }
        if ($res) {
            return self::ok();
        } else {
            return self::err();
        }
    }

    public function logout(Request $request)
    {
        $user = $this->getUInfo;
        $res = TM::find($user->id)->update(['session_key' => '']);
        if ($res) {
            Func::cacheApiToken('a_user_' . $user->id, null);
            return self::ok();
        } else {
            return self::err();
        }
    }

    /**
     * 上传APP
     * @param $request
     * @return array
     */
    public function uploadApp(Request $request)
    {
        // 检查字段
        $validator = AUserValidator::uploadFile($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $info = $request->file('file');
        $max_size = config('params.file.app.size');
        $size = $info->getSize();
        if (empty($info)) {
            return self::err('请选择要上传的文件');
        }
        //大小
        if ($size > $max_size * 1024 * 1024) {
            return self::err('文件大小不能超过' . $max_size . 'M');
        }
        $save_name = $info->store('app', 'public');
        if ($save_name) {
            return self::ok([
                'full_link' => url('/storage/' . $save_name),
                'file_link' => '/storage/' . $save_name,
                'file_size' => Func::fileSizeFormat($size),
            ]);
        } else {
            return self::err('文件上传失败');
        }
    }


}
