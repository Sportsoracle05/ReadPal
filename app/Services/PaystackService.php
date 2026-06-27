<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key');
        $this->baseUrl   = rtrim(config('paystack.payment_url'), '/');

        if (empty($this->secretKey)) {
            throw new \RuntimeException('Paystack secret key is not configured.');
        }
    }

    // ─── HTTP Client ──────────────────────────────────────────────────────────

    private function http(): PendingRequest
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache',
        ])->timeout(30)->retry(2, 500);
    }

    // ─── Reference Generation ─────────────────────────────────────────────────

    /**
     * Generate a cryptographically random, unique transaction reference.
     */
    public function generateReference(string $prefix = 'RP'): string
    {
        return strtoupper($prefix) . '_' . now()->format('YmdHis') . '_' . Str::random(10);
    }

    // ─── Transaction Initialization ───────────────────────────────────────────

    /**
     * Initialize a Paystack transaction and return the authorization URL.
     *
     * @param  string  $email
     * @param  int     $amount      Amount in kobo (NGN × 100)
     * @param  string  $reference
     * @param  array   $metadata
     * @param  string  $callbackUrl
     * @return array{status: bool, authorization_url: string|null, message: string}
     */
    public function initializeTransaction(
        string $email,
        int    $amount,
        string $reference,
        array  $metadata    = [],
        string $callbackUrl = ''
    ): array {
        try {
            $response = $this->http()->post("{$this->baseUrl}/transaction/initialize", [
                'email'        => $email,
                'amount'       => $amount,
                'reference'    => $reference,
                'callback_url' => $callbackUrl ?: route('payment.callback'),
                'metadata'     => array_merge($metadata, [
                    'cancel_action' => route('payment.cancel'),
                ]),
                'currency'     => 'NGN',
                'channels'     => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                return [
                    'status'            => true,
                    'authorization_url' => $body['data']['authorization_url'],
                    'access_code'       => $body['data']['access_code'],
                    'message'           => $body['message'] ?? 'Initialized',
                ];
            }

            Log::warning('[Paystack] Init failed', ['body' => $body, 'ref' => $reference]);

            return [
                'status'  => false,
                'authorization_url' => null,
                'message' => $body['message'] ?? 'Transaction initialization failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('[Paystack] Init exception', ['error' => $e->getMessage(), 'ref' => $reference]);

            return [
                'status'            => false,
                'authorization_url' => null,
                'message'           => 'A network error occurred. Please try again.',
            ];
        }
    }

    // ─── Transaction Verification ─────────────────────────────────────────────

    /**
     * Verify a transaction by reference. Always call this server-side.
     *
     * @return array{status: bool, data: array|null, message: string}
     */
    public function verifyTransaction(string $reference): array
    {
        // Sanitize reference — only allow safe characters
        $reference = preg_replace('/[^A-Za-z0-9_\-]/', '', $reference);

        if (empty($reference)) {
            return ['status' => false, 'data' => null, 'message' => 'Invalid reference.'];
        }

        try {
            $response = $this->http()->get("{$this->baseUrl}/transaction/verify/{$reference}");
            $body     = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                return [
                    'status'  => true,
                    'data'    => $body['data'],
                    'message' => $body['message'] ?? 'Verified',
                ];
            }

            Log::warning('[Paystack] Verify failed', ['body' => $body, 'ref' => $reference]);

            return [
                'status'  => false,
                'data'    => null,
                'message' => $body['message'] ?? 'Verification failed.',
            ];
        } catch (\Throwable $e) {
            Log::error('[Paystack] Verify exception', ['error' => $e->getMessage(), 'ref' => $reference]);

            return [
                'status'  => false,
                'data'    => null,
                'message' => 'Unable to verify transaction at this time.',
            ];
        }
    }

    // ─── Webhook Signature Verification ──────────────────────────────────────

    /**
     * Verify Paystack webhook HMAC-SHA512 signature.
     * Call this FIRST before processing any webhook payload.
     */
    public function validateWebhookSignature(string $payload, string $signature): bool
    {
        $expected = hash_hmac('sha512', $payload, $this->secretKey);

        return hash_equals($expected, $signature);
    }

    // ─── Helper ───────────────────────────────────────────────────────────────

    /**
     * Convert kobo to Naira for display.
     */
    public static function toNaira(int $kobo): string
    {
        return '₦' . number_format($kobo / 100, 2);
    }
}
