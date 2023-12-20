<?php

namespace App\Http\Controllers;

use App\Models\GroupChat;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\NewMessage;

class GroupChatController extends Controller
{
    public function index()
    {
        $userGroups = auth()->user()->groups()->simplePaginate(6);
        // select only the groups the user can view
        // $userGroups = $userGroups->filter(function ($group) {
        //     return auth()->user()->can('view', $group);
        // });

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

    public function edit(GroupChat $groupChat)
    {
        try {
            $this->authorize('update', $groupChat);
            // make variables for accepted and waiting members
            $acceptedMembers = $groupChat->members->filter(function ($member) {
                return $member->pivot->status == 'accepted';
            });
            $waitingMembers = $groupChat->members->filter(function ($member) {
                return $member->pivot->status == 'waiting';
            });
            return view('group_chats.edit', compact('groupChat', 'acceptedMembers', 'waitingMembers'));
        } catch (AuthorizationException) {
            return redirect()->back()->withErrors(['message' => 'You are not authorized to edit this group chat']);
        }
    }

    public function create(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'usernames' => 'required|array',
            'usernames.*' => 'exists:users,username',
        ]);

        // Create a new GroupChat
        $groupChat = new GroupChat;
        $groupChat->name = $validatedData['name'];
        $groupChat->description = $validatedData['description'];
        
        // Set the authenticated user as the owner of the group chat
        $groupChat->owner_id = auth()->id();
        $groupChat->save();

        // Add the authenticated user to the usernames array
        $usernames = $validatedData['usernames'];
        $usernames[] = auth()->user()->username;

        // Find the User models for each username
        $users = User::whereIn('username', $usernames)->get();

        // Add each user to the group chat except for the creator
        foreach ($users as $user) {
            if ($user->user_id != auth()->id()) {
                $groupChat->addMember($user);
            }
        }

        // Save the group chat
        $groupChat->save();

        // Redirect to the new group chat page
        return redirect('/group-chats/' . $groupChat->group_id);
    }

    public function addMember(Request $request, GroupChat $groupChat)
    {
        // Validate the request
        $validatedData = $request->validate([
            'username' => 'required|string|exists:users,username',
        ]);

        // Find the User model for the username
        $user = User::where('username', $validatedData['username'])->first();

        if($groupChat->members->contains($user)) {
            return response()->json('Member already added');
        }

        // Add the user to the group chat
        $groupChat->addMember($user);

        // Save the group chat
        $groupChat->save();

        // return json response that removed member
        return response()->json('Member added');  
    }

    public function removeMember(Request $request, GroupChat $groupChat)
    {
        // Validate the request
        $validatedData = $request->validate([
            'username' => 'required|string|exists:users,username',
        ]);

        // Find the User model for the username
        $user = User::where('username', $validatedData['username'])->first();

        // Remove the user from the group chat
        $groupChat->removeMember($user);

        // Save the group chat
        $groupChat->save();

        return response()->json('Removed member');
    }

    public function update(Request $request, GroupChat $groupChat)
    {
        // Validate the request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        // Update the group chat
        $groupChat->name = $validatedData['name'];
        $groupChat->description = $validatedData['description'];
        $groupChat->save();

        // Redirect to the group chat page
        // return redirect('/group-chats/' . $groupChat->group_id);
        return response()->json('Group chat updated');
    }

    public function delete(Request $request)
{
    // Validate the request
    $validatedData = $request->validate([
        'group_id' => 'required|integer|exists:group_chats,group_id',
    ]);

    try {
        // Authorize the user
        $this->authorize('update', GroupChat::find($validatedData['group_id']));

        // Delete the group chat
        GroupChat::where('group_id', $validatedData['group_id'])->delete();

        // Redirect to a success page
        return redirect(route('group-chats.index'))->with(['message' => 'Group chat deleted successfully']);
    } catch (AuthorizationException $e) {
        // Redirect to a different page if the user does not have the permission to delete the group chat
        return redirect(route('group-chats.index') . $validatedData['group_id'])->withErrors(['message' => 'You are not authorized to delete this group chat']);
    }
}

    public function sendMessage(Request $request, GroupChat $groupChat)
    {
        try {
            $this->authorize('send', $groupChat);
        } catch (AuthorizationException $e) {
            // Redirect to a different page if the user does not have the permission to send a message
            return redirect('/group_chats' . $groupChat->id)->withErrors(['message' => 'You are not authorized to send a message to this group chat']);
        }

        $message = new Message;
        $message->content = $request->input('content');
        $message->emitter_id = auth()->id();
        $message->group_id = $groupChat->group_id;
        $message->date = date('Y-m-d');
        $message->viewed = false;
        $message->save();

        // Broadcast the message
        broadcast(new NewMessage($message, auth()->user()->name))->toOthers();

        return response()->json('Message sent');
    }

    public function getMessages(GroupChat $groupChat)
    {
        try {
            $this->authorize('view', $groupChat);
        } catch (AuthorizationException $e) {
            // Redirect to a different page if the user does not have the permission to send a message
            return redirect('/group_chats' . $groupChat->id)->withErrors(['message' => 'You are not authorized to view this group chat']);
        }
        return response()->json($groupChat->messages()->with('emitter')->get());

    }

    public function acceptInvite(Request $request, GroupChat $groupChat)
    {
        // Get the current user
        $user = $request->user();

        // Check if the user is a member of the group chat
        if ($groupChat->members->contains($user)) {
            // Update the status in the pivot table
            $groupChat->members()->updateExistingPivot($user->user_id, ['status' => 'accepted']);
            
            return redirect()->back()->with('message', 'You have accepted the invite.');
        } else {
            return redirect()->back()->withErrors(['message' => 'You are not a member of this group chat.']);
        }
    }

    public function rejectInvite(Request $request, GroupChat $groupChat)
    {
        // Get the current user
        $user = $request->user();

        // Check if the user is a member of the group chat
        if ($groupChat->members->contains($user)) {
            // Update the status in the pivot table
            // $groupChat->members()->updateExistingPivot($user->user_id, ['status' => 'rejected']);
            // remove the user from the group chat
            $groupChat->removeMember($user);
            
            return redirect()->back()->with('message', 'You have rejected the invite.');
        } else {
            return redirect()->back()->withErrors(['message' => 'You are not a member of this group chat.']);
        }
    }

    public function getMembers(GroupChat $groupChat)
    {
        return response()->json($groupChat->acceptedMembers()->get());
    }
}
