<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return Socialite::driver('canvas')->redirect();
    }

    public function callback()
    {
        /** @var \SocialiteProviders\Manager\OAuth2\User $canvasUser */
        $canvasUser = Socialite::driver('canvas')->user();
        $email = $canvasUser->accessTokenResponseBody['user']['name'];
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $email,
                'password' => Hash::make(Str::random(32)),
            ]
        );
        auth()->login($user);

        return redirect()->route('home');
    }
}
