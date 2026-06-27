<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        $isMaintenance = config('app.is_maintenance');

        if ($isMaintenance == true) {
            // If maintenance is ON, redirect everything to maintenance page
            if (!$request->is('maintenance*')) {
                return redirect()->route('maintenance');
            }
        } else {
            // If maintenance is OFF, prevent users from seeing the maintenance page
            if ($request->is('maintenance*')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
