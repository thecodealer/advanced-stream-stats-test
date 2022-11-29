<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class GoogleController extends Controller {
    public function redirect() {
        return Socialite::driver('google')->redirect();
    }

    public function callback() {
        $googleUser = Socialite::driver('google')->user();
 
        $user = User::where('email', $googleUser->user['email'])->first();
     
        if ($user) {
            $user->update([
                'name' => $googleUser->user['name'],
            ]);
        }
        else {
            $user = User::create([
                'name' => $googleUser->user['name'],
                'email' => $googleUser->user['email'],
                'password' => Hash::make(Str::random(20)),
            ]);
        }
     
        Auth::login($user);
     
        return redirect('/dashboard');
    }
}