<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['rep', 'super'])) {
            return $next($request);
        }

        // Redirect to user dashboard if not admin
        return redirect()->route('dashboard')->with('error', 'Access denied.');
    }
}
