<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function show($id) {
        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found'); 
        }
        
        $posts = $user->posts()->orderBy('date', 'desc')->get();
        
        return view('pages.users', ['user' => $user]);
    }
}
