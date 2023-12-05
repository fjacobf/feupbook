<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class GroupChatController extends Controller
{
    public function index()
    {
        $userGroups = auth()->user()->groups()->get();

        return view('group_chats.index', compact('userGroups'));
    }

    public function show(GroupChat $groupChat)
    {
        try
        {    
            $this->authorize('view', $groupChat);
            $groupChat->load('owner', 'messages.emitter');
            return view('group_chats.show', compact('groupChat'));
        }
        catch(AuthorizationException){
            return redirect()->back()->withErrors(['message' => 'You are not authorized to view this group chat']);
        }
    }
}
