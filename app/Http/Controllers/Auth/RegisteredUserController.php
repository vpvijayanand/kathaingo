<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

use App\Services\CaptchaService;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $captcha = app(CaptchaService::class)->generate();
        return view('auth.register', compact('captcha'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'mobile' => ['required', 'string', 'max:15'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'location' => ['nullable', 'string', 'max:255'],
            'dob' => ['nullable', 'date'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)
                    ->max(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                function ($attribute, $value, $fail) {
                    $commonWeak = ['password', '12345678', 'qwertyui', 'password123', 'admin123', 'welcome123', 'kathaingo123', 'kathaingo'];
                    if (in_array(strtolower($value), $commonWeak)) {
                        $fail('The selected password is too weak and easily guessable.');
                    }
                }
            ],
            'captcha_answer' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!app(CaptchaService::class)->verify($value)) {
                        $fail('The verification challenge answer is incorrect. Please try again.');
                    }
                }
            ],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'gender' => $request->gender,
            'location' => $request->location,
            'dob' => $request->dob,
            'password' => Hash::make($request->password),
            'is_approved' => true,
            'role' => 'author',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
