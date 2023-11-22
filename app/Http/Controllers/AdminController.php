<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function showUserManagement($id) {
        if (!(Auth::check() && auth()->user()->user_type == 'admin')) {
            abort(403, "Forbidden");
        }

        $user = User::find($id);
        if (!$user) {
            abort(404, 'User not found'); 
        }

        return view('pages.admin_manage_user', ['user' => $user]);
    }
}
