<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProviderController extends Controller
{
    public function redirect($provider){
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider){
        try{
            $SocialUser = Socialite::driver($provider)->user();

            $user = User::where([
                'provider' => $provider,
                'provider_id' => $SocialUser->id
            ])->first();

            if(!$user){
                $user = User::create([
                    'name' => $SocialUser->getName(),
                    'email' => $SocialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $SocialUser->getId(),
                    'provider_token' => $SocialUser->token,
                    'username' => User::generateUserName($SocialUser->getNickname()),
                    'password' => null,
                    'private' => false,
                    'user_type' => 'normal_user',
                ]);
            }
            else{
                $user->update([
                    'provider_token' => $SocialUser->token,
                ]);
            }

        } catch(\Exception $e){
            return redirect()->route('welcome')->withErrors('Something went wrong with your ' . $provider . ' login.');
        }        
    
        Auth::login($user);

        return redirect()->route('home');
    }
}
