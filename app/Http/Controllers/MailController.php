<?php

namespace App\Http\Controllers;

use App\Mail\MailModel;
use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller
{
    public function sendRecoveryEmail(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        $user = User::where('email', $email)->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => now()
            ]);

            $mailData = [
                'name' => $user->name,
                'token' => $token,
                'email' => $email,
            ];

            Mail::to($email)->send(new MailModel($mailData));

            return back()->with('message', 'We have e-mailed your password reset link!');
        } else {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
    }
}
