<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Models\LoginActivity;
use Illuminate\Http\Request;

class LogFailedLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Failed $event): void
    {
        LoginActivity::create([
            'user_id' => $event->user ? $event->user->id : null,
            'email' => $event->credentials['email'] ?? null,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'is_successful' => false,
            'logged_at' => now(),
        ]);
    }
}
