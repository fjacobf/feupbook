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

    public function followStatus() {
        $authUser = Auth::user();
        $statusRows = $this->followRequests()->get();
        
        $statusRow = $statusRows->where('req_id', $authUser->user_id)->first();
    
        if ($statusRow) {
            return $statusRow->status;
        }
    
        return '';
    }

    public function isFollowing() {
        if (Auth::check()) {
            $authUser = Auth::user();
    
            // Print the authenticated user's ID and the current instance's ID for debugging
            echo "Auth User ID: " . $authUser->user_id . PHP_EOL;
            echo "Current Instance ID: " . $this->user_id . PHP_EOL;
    
            // Print all follow requests for debugging
            $followRequests = $this->followRequests()->get();
            echo "Follow Requests: " . $followRequests . PHP_EOL;
    
            // Check if there's a follow request with accepted status and matching req_id
            $isFollowing = $this->followRequests()
                ->where('status', 'accepted')
                ->where('req_id', $authUser->user_id)
                ->exists();
    
            // Print the result for debugging
            echo "Is Following: " . ($isFollowing ? 'true' : 'false') . PHP_EOL;
    
            return $isFollowing;
        }
    
        return false;
    }     

    // For follwer counts.
    public function followRequests() {
        return $this->hasMany(FollowRequest::class, 'rcv_id');
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

