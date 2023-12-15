<?php

namespace App\Policies;

use App\Models\User;
use App\Models\GroupChat;
use Illuminate\Support\Facades\Log;

class GroupChatPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user, GroupChat $groupChat)
    {
        $members = $groupChat->members;
        return ($members->contains($user) && $members->where('user_id', $user->user_id)->first()->pivot->status == 'accepted');
    }

    public function send(User $user, GroupChat $groupChat)
    {
        $members = $groupChat->members;
        // return true if users is member and pivot status is accepted
        return ($members->contains($user) && $members->where('user_id', $user->user_id)->first()->pivot->status == 'accepted');
    }

    public function update(User $user, GroupChat $groupChat)
    {
        // check if user is owner
        return ($groupChat->owner_id == $user->user_id);

    }
}