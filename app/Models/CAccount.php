<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CAccount extends Model
{
    use SoftDeletes;

    protected $table = 'c_accounts';

    protected $fillable = [
        'uid',
        'username',
        'mode', // 账号类型 phone, email
    ];

    public function cUser()
    {
        return $this->belongsTo(CUser::class, 'uid', 'id');
    }

    // 查询是否存在
    public static function isExist($field, $uid = null){
        $data = self::where($field)->first();
        if ($data){
            if ($uid){
                if ($uid != $data->uid){
                    return true;
                }
            }else{
                return true;
            }
        }
        return false;
    }
}
