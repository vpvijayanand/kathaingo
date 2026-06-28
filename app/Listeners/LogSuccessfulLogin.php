<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event): void
    {
        LoginActivity::create([
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'is_successful' => true,
            'logged_at' => now(),
        ]);
    }
}
