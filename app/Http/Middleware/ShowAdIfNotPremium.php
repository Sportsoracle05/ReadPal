<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShowAdIfNotPremium
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && !auth()->user()->is_premium) {

            // Prevent infinite loop + frequency control
            if (
                !$request->session()->has('ad_shown') &&
                (!session()->has('last_ad_time') ||
                 now()->diffInMinutes(session('last_ad_time')) > 10)
            ) {
                session([
                    'ad_shown' => true,
                    'last_ad_time' => now()
                ]);

                return redirect()->route('ads.show', [
                    'redirect' => url()->full()
                ]);
            }
        }

        return $next($request);
    }
}