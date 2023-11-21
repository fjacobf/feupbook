<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\FollowRequest;
use App\Models\Post;

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

    public function getRouteKeyName() {
        return 'user_id';
    }

    // For following counts.
    public function followRequestsRcv() {
        return $this->hasMany(FollowRequest::class, 'rcv_id');
    }

    public function followerCounts() {
        return $this->followRequestsRcv()->where('status', 'accepted')->count();
    }

    public function isFollowing() {
        if (Auth::check()) {
            $authUser = Auth::user();
            return $this->followRequests()
            ->where('status', 'accepted')
            ->where('req_id', $authUser->user_id)
            ->exists();
        }
        return false;
    }

    // For follwer counts.
    public function followRequests() {
        return $this->hasMany(FollowRequest::class, 'req_id');
    }

    public function followingCounts() {
        return $this->followRequests()->where('status', 'accepted')->count();
    }

    // For post counts.
    public function posts() {
        return $this->hasMany(Post::class, 'owner_id');
    }
    public function postCounts() {
        return $this->posts()->count();
    }
}

