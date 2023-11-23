<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FollowRequest;
use Illuminate\Support\Facades\Log;

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

    

    public function search(Request $request){
        // Log::info('This is some useful information.');
        if($request){
            $data=User::where('username','like','%'.$request->search.'%')
            ->orwhere('email','like','%'.$request->search.'%')->get();
 
            $output='';
            if(count($data)>0){
                $output ='
                    <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                    </tr>
                    </thead>
                    <tbody>';
                        foreach($data as $row){
                            $output .='
                            <tr>
                            <th scope="row">'.$row->username.'</th>
                            <td>'.$row->email.'</td>
                            </tr>
                            ';
                        }
                $output .= '
                    </tbody>
                    </table>';
            }
            else{
                $output .='No results';
            }

            return $output;
        }
    }
    
}
