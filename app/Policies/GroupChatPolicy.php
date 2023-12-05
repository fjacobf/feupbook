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
        Log::info('view group chats policy');
        return $user->groups->contains($groupChat);
    }

}
