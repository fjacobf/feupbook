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
        if($members->contains($user)) {
            return true;
        } else {
            return false;
        }    
    }

    public function send(User $user, GroupChat $groupChat)
    {
        $members = $groupChat->members;
        if($members->contains($user)) {
            return true;
        } else {
            return false;
        }
    }

    public function update(User $user, GroupChat $groupChat)
    {
        // check if user is owner
        if($groupChat->owner_id == $user->user_id) {
            return true;
        } else {
            return false;
        }
    }
}