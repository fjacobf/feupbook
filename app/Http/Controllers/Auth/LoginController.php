<?php
 
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

use App\Models\User;

class LoginController extends Controller
{

    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/home');
        } else {
            return view('auth.login');
        }
    }

    public function showResetPasswordForm()
    {
        return view('auth.resetPassword');
    }

    public function recoverPassword($token) {
        return view('auth.recoverPassword', ['token' => $token]);
    }

    public function newPasswordFromEmail(Request $request, $token) {
        // Validate token and change password
        $request->validate([
            'new_password' => ['required', 'confirmed'],
        ]);

        $password = $request->new_password;

        $email = DB::table('password_resets')->where('token', $token)->value('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->save();

            return redirect()->route('login')
                ->withSuccess('You have successfully changed your password!');
        } else {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->user_type == 'deleted') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Cannot access account. Please contact support.',
                ])->onlyInput('email');
            }
            
            // go to welcome page
            return redirect()->route('home')
                ->withSuccess('You have successfully logged in!');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->withSuccess('You have logged out successfully!');
    } 
}
