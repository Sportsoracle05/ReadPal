<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->is('admin/*') || $request->is('api/*')) {
            return $response;
        }

        if (!$request->session()->has('visitor_id')) {
            $request->session()->put('visitor_id', Str::uuid()->toString());
        }

        $sessionId = $request->session()->get('visitor_id');

        $materialId = null;
        $route = $request->route();

        if ($route) {
            // If route has 'material' parameter (bound model or id/slug)
            if ($route->hasParameter('material')) {
                $param = $route->parameter('material');

                if (is_object($param) && isset($param->id)) {
                    $materialId = $param->id;
                } elseif (is_numeric($param)) {
                    $materialId = (int)$param;
                } elseif (is_string($param)) {
                    // try to resolve by slug (defensive)
                    $m = \App\Models\Material::where('slug', $param)->first();
                    if ($m) $materialId = $m->id;
                }
            }

            // fallback: maybe parameter named 'slug' contains material slug
            if (is_null($materialId) && $route->hasParameter('slug')) {
                $slugParam = $route->parameter('slug');
                if (is_object($slugParam) && isset($slugParam->id)) {
                    $materialId = $slugParam->id;
                } elseif (is_string($slugParam)) {
                    $m = \App\Models\Material::where('slug', $slugParam)->first();
                    if ($m) $materialId = $m->id;
                }
            }
        }

        // DEBUG: log when material param exists but not resolved
        if ($route && ($route->hasParameter('material') || $route->hasParameter('slug')) && is_null($materialId)) {
            Log::debug('TrackVisits: material param present but not resolved', [
                'route_parameters' => $route->parameters(),
                'path' => $request->path(),
            ]);
        }

        // Create visit (make sure Visit model allows material_id)
        Visit::create([
            'material_id' => $materialId,
            'session_id' => $sessionId,
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'page' => $request->path(),
            'visited_at' => now()->utc(),
        ]);

        return $response;
    }
}
