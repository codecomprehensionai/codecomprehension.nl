<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CanvasSocialiteController extends Controller
{
    public function redirect(Request $request)
    {
        // return \Socialite::driver('canvas')->redirect();
    }

    public function callback(Request $request)
    {
        // $user = \Socialite::driver('canvas')->user();

        // Handle the user information as needed
        // For example, you can log in the user or create a new user record
        // Auth::login($user);

        // return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        // Auth::logout();
        // return redirect()->route('home');
    }
}
