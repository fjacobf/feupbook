<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function index()
    {
        $groupChats = GroupChat::all();
        return view('group_chats.index', compact('groupChats'));
    }

    public function show(GroupChat $groupChat)
    {
        $groupChat->load('owner', 'messages.emitter');
        return view('group_chats.show', compact('groupChat'));
    }


    // Add other CRUD operations as needed
}
