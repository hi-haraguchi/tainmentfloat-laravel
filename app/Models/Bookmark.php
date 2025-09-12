<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    /** @use HasFactory<\Database\Factories\BookmarkFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thought_id',
    ];

    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Thought
    public function thought()
    {
        return $this->belongsTo(Thought::class);
    }
}
