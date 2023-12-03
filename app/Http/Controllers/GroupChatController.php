<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function index()
    {
        // Fetch only the groups where the current user is a member
        $userGroups = auth()->user()->groups()->get();

        return view('group_chats.index', compact('userGroups'));
    }

    public function show(GroupChat $groupChat)
    {
        $this->authorize('view', $groupChat);
        $groupChat->load('owner', 'messages.emitter');
        return view('group_chats.show', compact('groupChat'));
    }


    // Add other CRUD operations as needed
}
