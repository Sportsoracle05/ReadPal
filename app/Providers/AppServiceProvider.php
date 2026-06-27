<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Ai\AssignmentAiService;
use App\Services\Ai\PromptBuilderService;
use App\Services\Ai\Providers\GeminiService;
use App\Services\Ai\Providers\GroqService;
use App\Services\Ai\Providers\OpenRouterService;
use App\Services\Ai\Providers\HuggingFaceService;
use App\Services\Ai\AiAssistantService;
use App\Services\Ai\KnowledgeRetrieverService;
use App\Services\Ai\QueryPreprocessorService;
use App\Services\Ai\ResponseFormatterService;
use App\Services\Ai\ProviderHealthService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\PrivateKarl;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PromptBuilderService::class);
        $this->app->singleton(GeminiService::class);
        $this->app->singleton(GroqService::class);
        $this->app->singleton(OpenRouterService::class);
        $this->app->singleton(HuggingFaceService::class);
        $this->app->singleton(QueryPreprocessorService::class);
        $this->app->singleton(ResponseFormatterService::class);

        $this->app->singleton(KnowledgeRetrieverService::class, function ($app) {
            return new KnowledgeRetrieverService(
                $app->make(QueryPreprocessorService::class)
            );
        });
        
        $this->app->singleton(ProviderHealthService::class, function ($app) {
            return new ProviderHealthService(
                $app->make(\App\Services\Ai\Providers\GeminiService::class),
                $app->make(\App\Services\Ai\Providers\GroqService::class),
                $app->make(\App\Services\Ai\Providers\OpenRouterService::class),
                $app->make(\App\Services\Ai\Providers\HuggingFaceService::class),
            );
        });
        
        $this->app->singleton(AssignmentAiService::class, function ($app) {
            return new AssignmentAiService(
                $app->make(QueryPreprocessorService::class),
                $app->make(KnowledgeRetrieverService::class),
                $app->make(PromptBuilderService::class),
                $app->make(GeminiService::class),
                $app->make(GroqService::class),
                $app->make(OpenRouterService::class),
                $app->make(ResponseFormatterService::class),
            );
        });

        $this->app->singleton(AiAssistantService::class, function ($app) {
            return new AiAssistantService(
                $app->make(QueryPreprocessorService::class),
                $app->make(KnowledgeRetrieverService::class),
                $app->make(ResponseFormatterService::class),
                $app->make(PromptBuilderService::class),
                $app->make(GeminiService::class),
                $app->make(GroqService::class),
                $app->make(OpenRouterService::class),
                $app->make(HuggingFaceService::class),
            );
        });
        
        $this->app->singleton(
            \App\Services\PaystackService::class,
            fn () => new \App\Services\PaystackService()
        );
    }
    
    public function boot(): void
    {   
        \Illuminate\Support\Facades\View::share(
            'paystackPublicKey',
            config('paystack.public_key')
        );
        
        
        // 1. Prune old DMs using the 5% lottery to save server resources
        // This runs the cleanup roughly 1 out of every 20 page loads.
        if (rand(1, 100) <= 5) {
            try {
                PrivateKarl::prune();
            } catch (\Exception $e) {
                // Silently fail (e.g. during migrations)
            }
        }

        // 2. Share $unreadCount with every view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $count = PrivateKarl::where('receiver_id', Auth::id())
                                    ->whereNull('viewed_at')
                                    ->count();
                $view->with('unreadCount', $count);
            } else {
                $view->with('unreadCount', 0);
            }
        });

        Event::listen(Login::class, function ($event) {
            Log::channel('logins')->info('User Login', [
                'id' => $event->user->id,
                'name' => $event->user->firstname,
                'ip' => request()->ip(),
                'time' => now()->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
