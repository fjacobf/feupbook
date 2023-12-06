<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Message;

class GroupChatController extends Controller
{
    public function index()
    {
        $userGroups = auth()->user()->groups()->get();

        return view('group_chats.index', compact('userGroups'));
    }

    public function show(GroupChat $groupChat)
    {
        try {
            $this->authorize('view', $groupChat);
            $groupChat->load('owner', 'messages.emitter');
            return view('group_chats.show', compact('groupChat'));
        } catch (AuthorizationException) {
            return redirect()->back()->withErrors(['message' => 'You are not authorized to view this group chat']);
        }
    }

    public function create()
    {
        return view('group_chats.create');
    }

    public function sendMessage(Request $request, GroupChat $groupChat)
{
    try {
        $this->authorize('send', $groupChat);
    } catch (AuthorizationException $e) {
        // Redirect to a different page if the user does not have the permission to send a message
        return redirect('/group-chats')->withErrors(['message' => 'You are not authorized to send a message to this group chat']);
    }

    $message = new Message;
    $message->content = $request->input('content');
    $message->emitter_id = auth()->id();
    $message->group_id = $groupChat->id;
    $message->date = date('Y-m-d');
    $message->viewed = false;
    $message->save();

    return response()->json('Message sent');
}
}
