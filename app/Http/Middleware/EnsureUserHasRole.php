<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Admins always have access to everything
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
