<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class NotificationController extends Controller
{
    public function list($id)
    {
      try{
        $this->authorize('viewAny', Notification::class);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = User::find($id);

        $notifications =  Notification::where('notified_user', $user->user_id)
                    ->orderBy('date', 'desc')->get();
        
        return view('pages.notifications', ['notifications' => $notifications]);
      } 
      catch(AuthorizationException $e){
        return redirect('/')->withErrors(['message' => 'Log in in order to notifications']);
      }
    }

    public function reload(Request $request)
    {
      try{
        $this->authorize('viewAny', Notification::class);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = User::find($request->input('query'));

        $notifications =  Notification::where('notified_user', $user->user_id)
                    ->orderBy('date', 'desc')->get();
        
        return view('partials.notification', compact('notifications'));
      } 
      catch(AuthorizationException $e){
        return redirect('/')->withErrors(['message' => 'Log in in order to notifications']);
      }
    }

}
