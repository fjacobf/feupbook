<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FollowRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{   
    public function show($id) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found'); 
        }
        
        $posts = $user->posts()->orderBy('created_at', 'desc')->get();

        return view('pages.users', ['user' => $user]);
    }

    public function follow($id) {
        $authUser = Auth::user();
        $user = User::find($id);

        if (!$user->private) {
            $followRequest = new FollowRequest([
                'req_id' => $authUser->user_id,
                'rcv_id' => $user->user_id,
                'date' => now(),
                'status' => 'accepted',
            ]);

            $followRequest->save();
        } else {
            $followRequest = new FollowRequest([
                'req_id' => $authUser->user_id,
                'rcv_id' => $user->user_id,
                'date' => now(),
                'status' => 'waiting',
            ]);

            $followRequest->save();
        }
        return redirect()->back();
    }

    public function unfollow($id) {
        $authUser = Auth::user();
        $user = User::find($id);
    
        $followRequest = FollowRequest::where('req_id', $authUser->user_id)
            ->where('rcv_id', $user->user_id);
        // dd($followRequest);
    
        if ($followRequest) {
            $followRequest->delete();
        }
    
        return redirect()->back();
    }

    public function showFollowerPage($id) {
        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found'); 
        }
        
        $followers = $user->followers()->get();

        return view('pages.followers', ['user' => $user, 'followers' => $followers]);
    }

    public function showFollowingPage($id) {
        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found'); 
        }
        
        $following = $user->following()->get();

        return view('pages.following', ['user' => $user, 'following' => $following]);
    }

    public function removeFollower($id) {
        $authUser = Auth::user();
        $user = User::find($id);
    
        $followRequest = FollowRequest::where('req_id', $user->user_id)
            ->where('rcv_id', $authUser->user_id);
    
        if ($followRequest) {
            $followRequest->delete();
        }
    
        return redirect()->back();
    }

    public function showEditPage($id) {
        $user = User::find($id);

        $this->authorize('updateSelf', $user);

        if (!$user) {
            abort(404, 'User not found'); 
        }

        return view('pages.edit_profile', ['user' => $user]);
    }

    public function updateProfile($id) {
        $user = User::find($id);

        $this->authorize('updateSelf', $user);

        if (!$user) {
            abort(404, 'User not found'); 
        }

        $validatedData = request()->validate([
            'name' => 'required|max:255',
            'username' => 'required|max:255|unique:users,username,' . $user->user_id. ',user_id',
            'private' => 'boolean',
            'bio' => 'max:255',
        ]);

        $user->update(array_merge($validatedData, ['private' => request('private', 0)]));

        return redirect()->route('user.profile', ['id' => $user->user_id]);
    }
    
    public function deleteProfile($id) {
        $user = User::find($id);

        $this->authorize('deleteSelf', $user);

        if (!$user) {
            abort(404, 'User not found'); 
        }

        $user->update(['user_type' => 'deleted']);

        if (Auth::check()) {
            Auth::logout();
        }

        return redirect()->route('home')->with('success', 'User deleted successfully.');
    }

    public function updatePassword($id) {
        $user = User::find($id);

        $this->authorize('updateSelf', $user);

        if (!$user) {
            abort(404, 'User not found'); 
        }

        $validatedData = request()->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => bcrypt($validatedData['new_password'])]);

        return redirect()->route('user.profile', ['id' => $user->user_id]);
    }
}
