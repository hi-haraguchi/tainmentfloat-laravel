<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LastRecord extends Model
{
    //
    protected $fillable = [
        'user_id',
        'kind',
        'last_recorded_at',
        'last_reminded_at', 
    ];

    protected $casts = [
        'last_recorded_at' => 'datetime',
        'last_reminded_at'  => 'datetime',
    ];
}
