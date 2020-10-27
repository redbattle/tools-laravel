<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JxHeating extends Model
{

    protected $table = 'property_heating';

    const UPDATED_AT = 'update_time';

    protected $keyType = 'string';

    protected $fillable = [
        'due',
    ];

}
