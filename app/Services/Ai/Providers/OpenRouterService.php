<?php

namespace App\Services\Ai\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenRouterService — Multi-Model Fallback
 *
 * OpenRouter is an API gateway that routes to 200+ models from one endpoint.
 * Used as Step 4 in the fallback chain when Gemini and Groq both fail.
 *
 * Why OpenRouter as fallback (not primary):
 *  - Adds slight latency vs direct provider calls
 *  - Costs slightly more per token (routing markup ~5%)
 *  - But: redundant across many models = near-zero downtime
 *
 * Free/cheap models to use as fallback (set in config):
 *  - deepseek/deepseek-chat          (free, high quality)
 *  - meta-llama/llama-3.1-8b-instruct:free  (free tier)
 *  - mistralai/mistral-7b-instruct:free     (free tier)
 *  - nousresearch/hermes-3-llama-3.1-405b   (paid, very capable)
 *
 * API: https://openrouter.ai/api/v1 (OpenAI-compatible)
 */
class OpenRouterService
{
    private string $apiKey;
    private string $model;
    private int    $timeoutSeconds;
    private string $appName;

    private const BASE_URL = 'https://openrouter.ai/api/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey         = config('services.openrouter.api_key', '');
        $this->model          = config('services.openrouter.model', 'deepseek/deepseek-chat');
        $this->timeoutSeconds = config('services.openrouter.timeout', 20);
        $this->appName        = config('app.name', 'ReadPal');
    }

    /**
     * Generate a response via OpenRouter.
     *
     * @param  string      $systemPrompt
     * @param  string      $userMessage
     * @param  string|null $overrideModel  Optionally switch model at call time
     */
    public function generate(
        string  $systemPrompt,
        string  $userMessage,
        ?string $overrideModel = null,
        string  $context = 'chat',
        ?int    $userId = null
    ): array {
        $startTime = \App\Services\Ai\AiProviderLogService::startTimer();
        $result = null;
        $model = $overrideModel ?? $this->model;
    
        if (empty($this->apiKey)) {
            $result = $this->failure('OpenRouter API key not configured.', $model);
        } else {
            $payload = [
                'model'       => $model,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userMessage],
                ],
                'temperature'  => 0.4,
                'max_tokens'   => 400,
                'top_p'        => 0.8,
            ];
    
            try {
                $response = Http::timeout($this->timeoutSeconds)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Content-Type'  => 'application/json',
                        'HTTP-Referer'  => config('app.url', 'https://localhost'),
                        'X-Title'       => $this->appName,
                    ])
                    ->post(self::BASE_URL, $payload);
    
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMsg  = $errorBody['error']['message'] ?? "HTTP {$response->status()}";
                    $result = $this->failure($errorMsg, $model);
                } else {
                    $data = $response->json();
                    $text = $data['choices'][0]['message']['content'] ?? null;
    
                    if (empty($text)) {
                        $result = $this->failure('OpenRouter returned empty response.', $model);
                    } else {
                        $result = [
                            'success'     => true,
                            'text'        => trim($text),
                            'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                            'provider'    => 'openrouter',
                            'model'       => $model,
                            'error'       => null,
                        ];
                    }
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $result = $this->failure('OpenRouter timed out.', $model);
            } catch (\Exception $e) {
                $result = $this->failure($e->getMessage(), $model);
            }
        }
    
        // ── Log the result before returning ──
        \App\Services\Ai\AiProviderLogService::record($result, $startTime, $context, $userId);
    
        return $result;
    }


    /**
     * Try multiple models in sequence until one works.
     * Useful when the primary OpenRouter model is rate-limited.
     */
    public function generateWithFallbackModels(
        string $systemPrompt,
        string $userMessage
    ): array {
        // Ordered list of models to try — prefer free ones first
        $models = config('services.openrouter.fallback_models', [
            'deepseek/deepseek-chat',
            'meta-llama/llama-3.1-8b-instruct:free',
            'mistralai/mistral-7b-instruct:free',
        ]);

        foreach ($models as $model) {
            $result = $this->generate($systemPrompt, $userMessage, $model);
            if ($result['success']) {
                return $result;
            }
            Log::info("OpenRouter model {$model} failed, trying next.");
        }

        return $this->failure('All OpenRouter fallback models failed.');
    }

    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    private function failure(string $error, ?string $model = null): array
    {
        return [
            'success'     => false,
            'text'        => '',
            'tokens_used' => 0,
            'provider'    => 'openrouter',
            'model'       => $model ?? $this->model,
            'error'       => $error,
        ];
    }
}
