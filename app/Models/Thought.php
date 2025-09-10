<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thought extends Model
{
    /** @use HasFactory<\Database\Factories\ThoughtFactory> */
    use HasFactory;

    protected $fillable = [
        'title_id',
        'year',
        'month',
        'day',
        'part',
        'thought',
        'tag_id',
        'link',
    ];

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
