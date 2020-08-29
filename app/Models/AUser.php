<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AUser extends Model
{
    use SoftDeletes;
    protected $table = 'a_users';

    protected $fillable = [
        'username',
        'password',
        'nickname',
        'session_key',
        'status',
    ];

    protected $hidden = [
        'session_key',
        'password',
    ];

    /**
     * 查询状态
     * @param $field
     * @return null|string
     */
    public static function isStatus($field){
        $data = self::where($field)->first();
        if ($data){
            if ($data->status == 0){
                return 'disable';
            }else if ($data->status == 1){
                return 'enable';
            }
        }
        return null;
    }

    /**
     * 查询是否存在
     * @param $field
     * @param null $id
     * @return bool
     */
    public static function isExist($field, $id = null){
        $data = self::where($field)->first();
        if ($data){
            if ($id){
                if ($id != $data->id){
                    return true;
                }
            }else{
                return true;
            }
        }
        return false;
    }
}
