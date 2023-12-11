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
        'bio',
        'private',
        'user_type',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'private' => 'boolean',
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

    public function followStatus() {
        if(Auth::check()) {
            $authUser = Auth::user();
            $statusRows = $this->followRequestsRcv()->get();
            
            $statusRow = $statusRows->where('req_id', $authUser->user_id)->first();
        
            if ($statusRow) {
                return $statusRow->status;
            }
        }
    
        return '';
    }

    public function isFollowing() {
        $status = $this->followStatus();
    
        // Check if the follow status is 'accepted'
        return $status === 'accepted';
    }
    
    // For follwer counts.
    public function followRequests() {
        return $this->hasMany(FollowRequest::class, 'req_id');
    }

    public function followingCounts() {
        return $this->followRequests()->where('status', 'accepted')->count();
    }

    public function postCounts() {
        return $this->posts()->count();
    }

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

    public function likedComments()
    {
        return $this->hasMany(Comment::class, 'user_id', 'user_id');
    }
    
}

