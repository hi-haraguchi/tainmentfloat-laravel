<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interval extends Model
{
    //
    protected $fillable = [
        'user_id',
        'kind',
        'interval_days',
        'use_custom',
    ];
}
