<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        // 'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function titles()
    {
        return $this->hasMany(Title::class);
    }

    public function bookmarks()
    {
    return $this->belongsToMany(Thought::class, 'bookmarks')
                ->withTimestamps();
    }

    public function lastRecords() {
    return $this->hasMany(LastRecord::class);
    }

    public function intervals() {
    return $this->hasMany(Interval::class);
    }

    public function remindSetting() {
    return $this->hasOne(RemindSetting::class);
    }

    protected static function booted()
    {
    static::created(function ($user) {
        // 全体レコード
        LastRecord::create([
            'user_id' => $user->id,
            'kind' => null,
            'last_recorded_at' => null,
        ]);

        // 6ジャンル分のintervals
        for ($kind = 0; $kind <= 5; $kind++) {
            Interval::create([
                'user_id' => $user->id,
                'kind' => $kind,
                'interval_days' => null,
                'use_custom' => false,
            ]);
        }

        // remind_settings
        RemindSetting::create([
            'user_id' => $user->id,
            'mode' => 0,
        ]);
    });
    }

}
