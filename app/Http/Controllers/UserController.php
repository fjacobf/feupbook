<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FollowRequest;

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
        
        $posts = $user->posts()->orderBy('date', 'desc')->get();

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
    
}
