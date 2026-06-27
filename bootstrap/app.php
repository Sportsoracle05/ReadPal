<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckMaintenanceMode; 
use Illuminate\Support\Facades\Log;
use Illuminate\Session\TokenMismatchException;
use App\Http\Middleware\VerifyPaystackWebhook;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
            Route::middleware('web')
                ->group(base_path('routes/paystack.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Add to Global Middleware (runs on every request)
        $middleware->append(CheckMaintenanceMode::class);
        
        $middleware->validateCsrfTokens(except: [
            'paystack/webhook',
        ]);
        
        // ✅ AD MIDDLEWARE (CORRECT PLACE)
        $middleware->appendToGroup('web', \App\Http\Middleware\ShowAdIfNotPremium::class);

        // 2. Your existing Aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'rep' => \App\Http\Middleware\EnsureRep::class,
            'super' => \App\Http\Middleware\EnsureSuperAdmin::class,
            'show.ad' => \App\Http\Middleware\ShowAdIfNotPremium::class,
        ]);
        
        // 3. Your existing Web Group logic
        $middleware->appendToGroup(
            'web',
            \App\Http\Middleware\TrackVisits::class
        );
    })
        ->withExceptions(function (Exceptions $exceptions) {
        
        // 1. Automatically redirect back or to a named route when session expires
        $exceptions->render(function (TokenMismatchException $e) {
             
            return redirect()->route('login')->with('info', 'Please log in again.');
        });
        
        $exceptions->report(function (\Throwable $e) {
            $user = auth()->user();
            $userId = $user ? "User ID: {$user->id}" : 'Guest';
            
            // Get just the error message and the file/line where it happened
            $message = $e->getMessage();
            $file = basename($e->getFile());
            $line = $e->getLine();

            // Log a single clean line to laravel.log
            Log::error("$userId | Error: $message | Location: $file:$line");

            // CRITICAL: Stop Laravel from writing the giant stack trace
            return false; 
        });
    })->create();

