<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'tag',
    ];

    public function thoughts()
    {
        return $this->hasMany(Thought::class);
    }
}
