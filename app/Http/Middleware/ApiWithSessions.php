<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware for SPA auth routes: provides session support without CSRF verification.
 * This is used instead of 'web' middleware to avoid CSRF token requirement on initial login.
 */
class ApiWithSessions
{
    /**
     * Handle the request.
     * We use 'web' middleware group for session support, but this is wrapped
     * by routes that are already excluded from CSRF verification.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
