<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Access\AuthorizationException;

class AdminController extends Controller
{
    public function showUserManagement($id) {
        try
        {    
            $user = User::findOrFail($id);
            
            $this->authorize('viewAdminInterface', $user);
            Log::info('after authorize');
            $userTypes = ['normal_user', 'admin', 'suspended'];

            return view('pages.admin_manage_user', compact('user', 'userTypes'));
        }
        catch(AuthorizationException $e){
            return redirect()->back()->withErrors(['message' => 'You are not authorized to view this page']);
        }
    }

    public function updateUser(Request $request, $id) {
        try
        {    
            $user = User::findOrFail($id);

            $this->authorize('updateAsAdmin', $user);

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
        catch(AuthorizationException $e){
            return redirect()->route('user.profile', ['id' => $user->user_id])
                        ->withErrors(['message' => 'You are not authorized to update the info of this user']);
        }
    }

    public function deleteUser($id) {
        try
        {    
            $user = User::findOrFail($id);

            $this->authorize('deleteAsAdmin', $user);

            $user->update(['user_type' => 'deleted']);

            return redirect()->route('home')->with('success', 'User deleted successfully.');
        }
        catch(AuthorizationException $e){
            return redirect()->route('home')->withErrors(['message' => 'You are not authorized to delete this user']);
        }
    }

    public function restoreUser($id) {
        try
        {    
            $user = User::findOrFail($id);

            $this->authorize('restoreAccount', $user);

            $user->update(['user_type' => 'normal_user']);

            return redirect()->route('home')->with('success', 'User restored successfully.');
        }
        catch(AuthorizationException $e){
            return redirect()->route('home')->withErrors(['message' => 'You are not authorized to restore this user']);
        }
    }
}
