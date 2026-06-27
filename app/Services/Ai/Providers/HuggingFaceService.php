<?php

namespace App\Services\Ai\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HuggingFaceService — Optional Experimental Fallback
 *
 * Uses HuggingFace Inference API (serverless).
 * Not recommended as primary — latency is unpredictable (cold start ~5-30s).
 * Useful as absolute last resort before DB-only fallback.
 *
 * Free tier: limited RPM, shared inference, no SLA.
 * PRO tier ($9/month): faster, priority queue.
 *
 * Best models for text generation via HF Inference API:
 *   - mistralai/Mistral-7B-Instruct-v0.2  (instruction-tuned, good quality)
 *   - HuggingFaceH4/zephyr-7b-beta         (fast, good at following instructions)
 *   - tiiuae/falcon-7b-instruct            (decent, free)
 *
 * NOTE: HF uses a different prompt format (no system/user split in older models).
 * We concatenate system + user into a single formatted prompt.
 */
class HuggingFaceService
{
    private string $apiKey;
    private string $model;
    private int    $timeoutSeconds;

    private const BASE_URL = 'https://api-inference.huggingface.co/models/';

    public function __construct()
    {
        $this->apiKey         = config('services.huggingface.api_key', '');
        $this->model          = config('services.huggingface.model', 'mistralai/Mistral-7B-Instruct-v0.2');
        $this->timeoutSeconds = config('services.huggingface.timeout', 30); // HF can be slow
    }

    /**
     * Generate a response from HuggingFace Inference API.
     */
    public function generate(string $systemPrompt, string $userMessage, string $context = 'chat', ?int $userId = null): array
    {
        $startTime = \App\Services\Ai\AiProviderLogService::startTimer();
        $result = null;
    
        if (empty($this->apiKey)) {
            $result = $this->failure('HuggingFace API key not configured.');
        } else {
            // HF uses Mistral instruction format
            $formattedPrompt = $this->formatPrompt($systemPrompt, $userMessage);
    
            $payload = [
                'inputs'     => $formattedPrompt,
                'parameters' => [
                    'max_new_tokens'  => 350,
                    'temperature'     => 0.4,
                    'top_p'           => 0.8,
                    'do_sample'       => true,
                    'return_full_text'=> false, 
                ],
            ];
    
            try {
                $response = Http::timeout($this->timeoutSeconds)
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->apiKey}",
                        'Content-Type'  => 'application/json',
                    ])
                    ->post(self::BASE_URL . $this->model, $payload);
    
                if ($response->status() === 503) {
                    $result = $this->failure('HuggingFace model is loading (cold start). Try again in 30s.');
                } elseif (!$response->successful()) {
                    $error = $response->json()['error'] ?? "HTTP {$response->status()}";
                    $result = $this->failure($error);
                } else {
                    $data = $response->json();
                    $text = is_array($data) ? ($data[0]['generated_text'] ?? null) : null;
    
                    if (empty($text)) {
                        $result = $this->failure('HuggingFace returned empty response.');
                    } else {
                        $text = $this->cleanResponse($text, $formattedPrompt);
                        $result = [
                            'success'     => true,
                            'text'        => trim($text),
                            'tokens_used' => 0, // HF free API doesn't report this
                            'provider'    => 'huggingface',
                            'model'       => $this->model,
                            'error'       => null,
                        ];
                    }
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $result = $this->failure('HuggingFace timed out.');
            } catch (\Exception $e) {
                $result = $this->failure($e->getMessage());
            }
        }
    
        // ── Log the attempt ──
        \App\Services\Ai\AiProviderLogService::record($result, $startTime, $context, $userId);
    
        return $result;
    }


    /**
     * Format prompt in Mistral instruction format.
     * Adjust this if you switch to a different model family.
     */
    private function formatPrompt(string $system, string $user): string
    {
        return "<s>[INST] <<SYS>>\n{$system}\n<</SYS>>\n\n{$user} [/INST]";
    }

    /**
     * Some HF models echo the prompt back. Remove it.
     */
    private function cleanResponse(string $text, string $prompt): string
    {
        // If the model echoed the instruction markers, strip from [/INST] onwards
        if (str_contains($text, '[/INST]')) {
            $text = substr($text, strrpos($text, '[/INST]') + 7);
        }

        return trim($text);
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
            'provider'    => 'huggingface',
            'model'       => $this->model,
            'error'       => $error,
        ];
    }
}
