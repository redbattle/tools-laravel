<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CUser extends Model
{
    use SoftDeletes;

    protected $table = 'c_users';

    protected $fillable = [
        'nickname',
        'avatar',
        'password',
        'session_key',
        'status',
    ];

    protected $hidden = [
        'session_key',
        'password',
    ];

    protected $appends = [
        'phone', 'email',
    ];

    protected function getPhoneAttribute()
    {
        return $this->accounts()->where(['mode' => 'phone'])->value('username');
    }

    protected function getEmailAttribute()
    {
        return $this->accounts()->where(['mode' => 'email'])->value('username');
    }

    public function accounts()
    {
        return $this->hasMany(CAccount::class, 'uid', 'id');
    }
}
