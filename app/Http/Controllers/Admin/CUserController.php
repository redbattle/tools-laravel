<?php

namespace App\Http\Controllers\Admin;

use App\Models\CAccount;
use App\Models\CUser as TM;
use App\Validators\Admin\CUserValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CUserController extends BaseController
{
    public function getList(Request $request)
    {
        $r_username = $request->input('username');
        $r_status = $request->input('status');
        $request['page'] = intval($request->input('pageNo', '1'));
        $r_page_size = intval($request->input('pageSize', '10'));
        // user 查询条件
        $where = [];
        if (!is_null($r_status)) {
            $where['status'] = $r_status;
        }
        // account 查询条件
        $where_account = [];
        if (!is_null($r_username)) {
            $where_account[] = ['username', 'like', '%' . $r_username . '%'];
        }
        $lists = TM::select([
            'id',
            'nickname',
            'status'
        ])->where($where)->whereHas('accounts', function ($query) use ($where_account) {
            $query->where($where_account);
        })->orderBy('id', 'desc')->paginate($r_page_size);
        return self::ok([
            'lists' => $lists,
            'status_lists' => config('cus_dict.status.default'),
        ]);
    }

    public function update(Request $request)
    {
        // 检查字段
        $validator = CUserValidator::update($request);
        if ($validator !== true) {
            return self::err($validator);
        }
        $r_id = $request->input('id');
        $r_nickname = $request->input('nickname');
        $r_password = $request->input('password');
        $r_status = $request->input('status');
        $r_phone = $request->input('phone');
        $r_email = $request->input('email');
        // 手机号邮箱
        if (CAccount::isExist(['username' => $r_phone], $r_id)) {
            return self::err('手机号已存在');
        }
        if (CAccount::isExist(['username' => $r_email], $r_id)) {
            return self::err('邮箱已存在');
        }
        $saveData = [
            'nickname' => $r_nickname,
            'status' => $r_status,
        ];
        if (!is_null($r_password)) {
            $saveData['password'] = bcrypt(md5($r_password));
        }
        DB::beginTransaction();
        $res = TM::where(['id' => $r_id])->update($saveData);
        if ($res) {
            $save_phone = CAccount::where(['uid' => $r_id, 'mode' => 'phone'])->update(['username' => $r_phone]);
            // 查询是否绑定邮箱
            $save_email = true;
            $email = CAccount::where(['uid' => $r_id, 'mode' => 'email'])->first();
            if ($email) {
                $save_email = CAccount::where(['uid' => $r_id, 'mode' => 'email'])->update(['username' => $r_email]);
            } else if ($r_email) {
                $save_email = CAccount::create([
                    'uid' => $r_id,
                    'username' => $r_email,
                    'mode' => 'email',
                ]);
            }
            if ($save_phone && $save_email) {
                DB::commit();
                return self::ok();
            } else {
                DB::rollBack();
                return self::err('更新失败');
            }
        } else {
            DB::rollBack();
            return self::err('修改失败');
        }
    }

}
