<?php

namespace App\Http\Controllers;

use App\Models\FollowRequest;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Log;

class FollowRequestController extends Controller
{
    public function accept(Request $request, $user){
        $notified_user = $request->user();
        FollowRequest::where('rcv_id', $notified_user->user_id)->where('req_id', $user)->update(['status' => 'accepted']);
        return redirect()->back()->with('message', 'You have accepted the follow request.');
    }

    public function reject(Request $request, $user){
        $notified_user = $request->user();
        FollowRequest::where('rcv_id', $notified_user->user_id)->where('req_id', $user)->update(['status' => 'rejected']);
        return redirect()->back()->with('message', 'You have rejected the follow request.');
    }
}

// update "follow_requests" set "status" = accepted where "notified_user" = {"user_id":2,"username":"alice_smith","email":"alice.smith@example.com","name":"Alice Smith","avatar":"1702594395.jpg","bio":"Mathematics enthusiast","private":true,"user_type":"normal_user","tsvectors":"'alic':1A,3B 'enthusiast':6C 'mathematics':5C 'smith':2A,4B"} and "user_id" = {"user_id":3,"username":"prof_jones","email":"prof.jones@example.com","name":"Professor Jones","avatar":"1702594478.jpg","bio":"Teaching Physics at University X","private":false,"user_type":"normal_user","tsvectors":"'at':7C 'jon':2A,4B 'physics':6C 'prof':3B 'professor':1A 'teaching':5C 'university':8C 'x':9C"}