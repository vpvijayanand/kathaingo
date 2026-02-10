<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function redirectToProvider($provider)
    {
        return \Laravel\Socialite\Facades\Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = \Laravel\Socialite\Facades\Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Unable to login using ' . $provider . '. Please try again.']);
        }

        $user = \App\Models\User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update social ID if not present
            $idField = $provider . '_id';
            if (!$user->$idField) {
                $user->$idField = $socialUser->getId();
                $user->save();
            }
        } else {
            $idField = $provider . '_id';
            $user = \App\Models\User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                $idField => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'password' => null, // Social users don't have a password initially
                'is_approved' => false,
                'email_verified_at' => now(), // Assume social email is verified
            ]);
        }

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
