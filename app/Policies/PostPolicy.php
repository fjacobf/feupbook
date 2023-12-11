<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class PostPolicy
{

    public function __construct(){
        //
    }
    
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        $owner = $post->user;

        if ($owner->private) {
            return ($user->user_id === $post->owner_id) || $user->user_type === 'admin';
        }

        return true;
    }


    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Auth::check() && $user->user_type != 'suspended';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $user->user_id === $post->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->user_id === $post->owner_id || $user->user_type == 'admin';
    }

    public function like(User $user, Post $post): bool
    {
        return $user->can('view', $post);
    }

    public function bookmark(User $user, Post $post): bool
    {
        return $user->can('view', $post);
    }
}
