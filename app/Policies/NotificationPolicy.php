<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->user_id == Auth::user()->user_id;
    }
}
