<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\Response;

class UserPolicy
{

    public function __construct(){
        //
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewAdminInterface(User $user, User $model): bool
    {
        return ($user->user_type == 'admin' && $model->user_type != 'admin') && $user->user_id != $model->user_id;
    }

    public function updateAsAdmin(User $user, User $userToUpdate)
    {
        return $user->user_type == 'admin' && $userToUpdate->user_type != 'admin';
    }

    public function deleteAsAdmin(User $user, User $userToDelete)
    {
        return $user->user_type == 'admin' && $userToDelete->user_type != 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateSelf(User $user, User $model): bool
    {
        return $user->user_id === $model->user_id;
    }

    public function updateAdmin(User $user, User $model): bool
    {
        return $user->user_type == 'admin';
    }

    public function viewFollowPages(User $user, User $model): bool
    {
        return ($user->user_id === $model->user_id || $user->user_type == 'admin') || $model->private == false;
    }

    public function deleteSelf(User $user, User $model): bool
    {
        return $user->user_id === $model->user_id;
    }

    public function restoreAccount(User $user, User $model): bool
    {
        return $user->user_type == 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
