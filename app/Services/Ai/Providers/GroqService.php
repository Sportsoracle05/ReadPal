<?php

namespace App\Services\Ai\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GroqService — Fast Fallback AI Provider
 *
 * Groq uses custom LPU hardware — responses often arrive in < 1 second.
 * This makes it ideal as a fallback when Gemini times out.
 *
 * Default model: llama-3.1-8b-instant
 *   - Free tier: 14,400 req/day, 500K tokens/day
 *   - Very fast, good quality for summarization
 *
 * Alternative models (set via config):
 *   - llama-3.1-70b-versatile  (more capable, fewer free tokens)
 *   - mixtral-8x7b-32768       (good for long context)
 *   - gemma2-9b-it              (Google's Gemma via Groq)
 *
 * Uses OpenAI-compatible API format — easy to maintain.
 */
class GroqService
{
    private string $apiKey;
    private string $model;
    private int    $timeoutSeconds;

    private const BASE_URL = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey         = config('services.groq.api_key', '');
        $this->model          = config('services.groq.model', 'llama-3.1-8b-instant');
        $this->timeoutSeconds = config('services.groq.timeout', 10); // Groq is fast, be strict
    }

    /**
     * Generate a response from Groq.
     *
     * @param  string $systemPrompt
     * @param  string $userMessage
     * @return array{success: bool, text: string, tokens_used: int, provider: string, error: string|null}
     */
    public function generate(string $systemPrompt, string $userMessage, string $context = 'chat', ?int $userId = null): array
    {
        $startTime = \App\Services\Ai\AiProviderLogService::startTimer();
        $result = null;
    
        if (empty($this->apiKey)) {
            $result = $this->failure('Groq API key not configured.');
        } else {
            // OpenAI-compatible format
            $payload = [
                'model'       => $this->model,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $userMessage],
                ],
                'temperature' => 0.4,
                'max_tokens'  => 400,
                'top_p'       => 0.8,
                'stream'      => false,
            ];
    
            try {
                $response = Http::timeout($this->timeoutSeconds)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Content-Type'  => 'application/json',
                    ])
                    ->post(self::BASE_URL, $payload);
    
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMsg  = $errorBody['error']['message'] ?? "HTTP {$response->status()}";
                    $result = $this->failure($errorMsg);
                } else {
                    $data = $response->json();
                    $text = $data['choices'][0]['message']['content'] ?? null;
    
                    if (empty($text)) {
                        $result = $this->failure('Groq returned empty response.');
                    } else {
                        $result = [
                            'success'     => true,
                            'text'        => trim($text),
                            'tokens_used' => $data['usage']['total_tokens'] ?? 0,
                            'provider'    => 'groq',
                            'model'       => $this->model,
                            'error'       => null,
                        ];
                    }
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $result = $this->failure('Groq timed out.');
            } catch (\Exception $e) {
                $result = $this->failure($e->getMessage());
            }
        }
    
        // ── Log the result before returning ──
        \App\Services\Ai\AiProviderLogService::record($result, $startTime, $context, $userId);
    
        return $result;
    }


    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    private function failure(string $error): array
    {
        return [
            'success'     => false,
            'text'        => '',
            'tokens_used' => 0,
            'provider'    => 'groq',
            'model'       => $this->model,
            'error'       => $error,
        ];
    }
}
