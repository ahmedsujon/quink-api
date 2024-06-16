<?php

namespace App\Http\Controllers\auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function getGoogleTokenFromWeb(Request $request)
    {
        // test token from web
        $user = Socialite::driver('google')->stateless()->user();
        $token = $user->token;

        echo $token;
    }
}
