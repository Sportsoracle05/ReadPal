<?php
namespace App\Services\Ai;

use App\Services\Ai\Providers\GeminiService;
use App\Services\Ai\Providers\GroqService;
use App\Services\Ai\Providers\OpenRouterService;
use App\Services\Ai\Providers\HuggingFaceService;
use Illuminate\Support\Facades\Cache;

class ProviderHealthService
{
    // Minimal test prompt — uses as few tokens as possible
    private const TEST_SYSTEM = 'You are a test. Reply with exactly: OK';
    private const TEST_USER   = 'Say OK';

    public function __construct(
        private readonly GeminiService      $gemini,
        private readonly GroqService        $groq,
        private readonly OpenRouterService  $openRouter,
        private readonly HuggingFaceService $huggingFace,
    ) {}

    /**
     * Test all providers and return their health status.
     * Results cached for 5 minutes to avoid wasting tokens on refresh.
     *
     * @param  bool $forceRefresh  Bypass cache (manual re-test)
     * @return array<string, array{
     *   configured: bool,
     *   online: bool,
     *   response_time_ms: int,
     *   model: string,
     *   error: string|null,
     *   tested_at: string
     * }>
     */
    public function testAll(bool $forceRefresh = false): array
    {
        $cacheKey = 'ai_provider_health';

        if (!$forceRefresh) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }

        $results = [
            'gemini'      => $this->testProvider('gemini'),
            'groq'        => $this->testProvider('groq'),
            'openrouter'  => $this->testProvider('openrouter'),
            'huggingface' => $this->testProvider('huggingface'),
        ];

        Cache::put($cacheKey, $results, 300); // 5-minute cache

        return $results;
    }

    /**
     * Test a single provider by name.
     */
    public function testOne(string $provider): array
    {
        $result = $this->testProvider($provider);

        // Update the cached result for just this provider
        $cached = Cache::get('ai_provider_health', []);
        $cached[$provider] = $result;
        Cache::put('ai_provider_health', $cached, 300);

        return $result;
    }

    private function testProvider(string $name): array
    {
        $service = match($name) {
            'gemini'      => $this->gemini,
            'groq'        => $this->groq,
            'openrouter'  => $this->openRouter,
            'huggingface' => $this->huggingFace,
            default       => null,
        };

        if (!$service) {
            return $this->notConfigured($name);
        }

        if (!$service->isAvailable()) {
            return [
                'configured'       => false,
                'online'           => false,
                'response_time_ms' => 0,
                'model'            => config("services.{$name}.model", 'N/A'),
                'error'            => 'API key not configured',
                'tested_at'        => now()->toIso8601String(),
            ];
        }

        $start  = microtime(true);
        $result = $service->generate(self::TEST_SYSTEM, self::TEST_USER);
        $ms     = (int) round((microtime(true) - $start) * 1000);

        return [
            'configured'       => true,
            'online'           => $result['success'],
            'response_time_ms' => $ms,
            'model'            => $result['model'] ?? config("services.{$name}.model", 'N/A'),
            'error'            => $result['success'] ? null : ($result['error'] ?? 'Unknown error'),
            'tested_at'        => now()->toIso8601String(),
        ];
    }

    private function notConfigured(string $name): array
    {
        return [
            'configured'       => false,
            'online'           => false,
            'response_time_ms' => 0,
            'model'            => 'N/A',
            'error'            => 'Unknown provider',
            'tested_at'        => now()->toIso8601String(),
        ];
    }
}
