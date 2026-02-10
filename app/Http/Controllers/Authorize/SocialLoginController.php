<?php

namespace App\Http\Controllers\Authorize;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
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
            return redirect()->route('login')->withErrors(['email' => 'Unable to login with ' . ucfirst($provider)]);
        }

        $user = \App\Models\User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Create new user
            $user = \App\Models\User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                $provider . '_id' => $socialUser->getId(),
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)), // Dummy password
                'is_approved' => false, // Require approval
            ]);
        } else {
            // Update existing user with social ID if missing
            $user->update([
                $provider . '_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar() ?? $user->avatar,
            ]);
        }

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('dashboard');
    }
}
