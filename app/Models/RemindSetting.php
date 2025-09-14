<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemindSetting extends Model
{
    //
    protected $fillable = [
        'user_id',
        'mode',
    ];
}
