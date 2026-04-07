<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkrole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next ,...$roles): Response
    {
          // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Verificar si el usuario tiene alguno de los roles permitidos
        if (!in_array(auth()->user()->role, $roles)) {
            return redirect('/dashboard');
        }
        return $next($request);
    }
}
