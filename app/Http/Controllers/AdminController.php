<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showUserManagement($id) {
        if (!(Auth::check() && auth()->user()->user_type == 'admin')) {
            abort(403, "Forbidden");
        }

        $user = User::findOrFail($id);

        $userTypes = ['normal_user', 'admin', 'suspended'];

        return view('pages.admin_manage_user', compact('user', 'userTypes'));
    }

    public function updateUser(Request $request, $id) {
        if (!(Auth::check() && auth()->user()->user_type == 'admin')) {
            abort(403, "Forbidden");
        }

        $user = User::findOrFail($id);

        $rules = [
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id. ',user_id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id. ',user_id',
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255',
            'private' => 'boolean',
            'password' => 'nullable|string|min:8',
            'user_type' => 'required|string|in:normal_user,admin,suspended'
        ];

        $requestData = array_filter($request->all());
        
        if ($request->has('private')) {
            $requestData['private'] = true;
        } else {
            $requestData['private'] = false;
        }

        $request->validate($rules);

        $user->update($requestData);

        //return redirect()->back()->with('success', 'User updated successfully.');
        return redirect()->route('user.profile', ['id' => $user->user_id])
                     ->with('success', 'User updated successfully.');
    }
}
