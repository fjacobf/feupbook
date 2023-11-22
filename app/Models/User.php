<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// Added to define Eloquent relationships.
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable // lower case plural
{
    use HasApiTokens, HasFactory, Notifiable;

    // Don't add create and update timestamps in database.
    public $timestamps  = false;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'user_type',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $primaryKey = 'user_id';

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'owner_id', 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'author_id', 'post_id');
    }

    public function following() {
        return $this->belongsToMany(User::class, 'follow_requests', 'req_id', 'rcv_id')
                    ->wherePivot('status', 'accepted'); 
    }
    
}

