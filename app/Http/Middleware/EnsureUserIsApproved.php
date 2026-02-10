<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->is_approved && !$request->user()->is_admin) {
             auth()->logout();
             return redirect()->route('login')->withErrors(['email' => 'Your account is pending approval by the admin.']);
        }

        return $next($request);
    }
}
