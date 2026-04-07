<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * API routes should not require CSRF tokens.
     * They use Sanctum tokens or session-based auth without CSRF.
     */
    protected $except = [
        'api/*',
    ];
}
