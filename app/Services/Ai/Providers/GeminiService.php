<?php

namespace App\Services\Ai\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GeminiService — Primary AI Provider
 *
 * Uses Google Gemini 1.5 Flash (not Pro) by default.
 * Flash is:
 *  - Faster (critical for UX)
 *  - Much cheaper per token
 *  - More than capable for summarization/rewriting tasks
 *  - Has a generous free tier
 *
 * API: https://generativelanguage.googleapis.com/v1beta/models
 *
 * Rate limits (free tier): 15 RPM, 1M tokens/day
 * Rate limits (paid):      1500 RPM
 */
class GeminiService
{
    private string $apiKey;
    private string $model;
    private int    $timeoutSeconds;

    // Gemini API base URL
    private const BASE_URL = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey         = config('services.gemini.api_key', '');
        $this->model          = config('services.gemini.model', 'gemini-1.5-flash');
        $this->timeoutSeconds = config('services.gemini.timeout', 15);
    }

    /**
     * Generate a response from Gemini.
     *
     * @param  string $systemPrompt   The system/role instructions
     * @param  string $userMessage    The user's question + context
     * @return array{success: bool, text: string, tokens_used: int, error: string|null}
     */
    public function generate(string $systemPrompt, string $userMessage, string $context = 'chat', ?int $userId = null): array
    {
        $startTime = \App\Services\Ai\AiProviderLogService::startTimer();
        $result = null;
    
        if (empty($this->apiKey)) {
            $result = $this->failure('Gemini API key not configured.');
        } else {
            $url = self::BASE_URL . "/{$this->model}:generateContent?key={$this->apiKey}";
            
            $payload = [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents' => [['role' => 'user', 'parts' => [['text' => $userMessage]]]],
                'generationConfig' => [
                    'temperature' => 0.7, 
                    'maxOutputTokens' => 5092,
                    'topP' => 0.95,
                    'topK' => 40,
                ],
                'safetySettings' => [
                    ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                    ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_ONLY_HIGH'],
                ],
            ];
    
            try {
                $response = Http::timeout($this->timeoutSeconds)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);
    
                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMsg  = $errorBody['error']['message'] ?? "HTTP {$response->status()}";
                    $result = $this->failure($errorMsg);
                } else {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    
                    if (empty($text)) {
                        $finishReason = $data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
                        $result = $this->failure("Gemini returned empty response. Reason: {$finishReason}");
                    } else {
                        $result = [
                            'success'     => true,
                            'text'        => trim($text),
                            'tokens_used' => $data['usageMetadata']['totalTokenCount'] ?? 0,
                            'provider'    => 'gemini',
                            'model'       => $this->model,
                            'error'       => null,
                        ];
                    }
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $result = $this->failure('Gemini timed out.');
            } catch (\Exception $e) {
                $result = $this->failure($e->getMessage());
            }
        }
    
        // ── Log the result exactly once before returning ──
        \App\Services\Ai\AiProviderLogService::record($result, $startTime, $context, $userId);
    
        return $result;
    }


    /**
     * Quick availability check — uses a tiny test prompt.
     * Called before main requests if needed.
     */
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
            'provider'    => 'gemini',
            'model'       => $this->model,
            'error'       => $error,
        ];
    }
}
