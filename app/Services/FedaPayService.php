<?php
// app/Services/FedaPayService.php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FedaPayService
{
    protected $publicKey;
    protected $secretKey;
    protected $environment;
    protected $baseUrl;

    public function __construct()
    {
        $this->publicKey = env('FEDAPAY_PUBLIC_KEY')
            ?? config('services.fedapay.public_key')
            ?? Setting::get('fedapay_public_key');

        $this->secretKey = env('FEDAPAY_SECRET_KEY')
            ?? config('services.fedapay.secret_key')
            ?? Setting::get('fedapay_secret_key');

        $this->environment = env('FEDAPAY_ENVIRONMENT', 'sandbox')
            ?? config('services.fedapay.environment')
            ?? Setting::get('fedapay_environment', 'sandbox');

        $this->baseUrl = $this->environment === 'production'
            ? 'https://api.fedapay.com'
            : 'https://sandbox-api.fedapay.com';

        Log::info('FedaPay initialized', [
            'environment' => $this->environment,
            'base_url' => $this->baseUrl,
            'has_secret' => !empty($this->secretKey),
        ]);
    }

    /**
     * Initiate payment with FedaPay - COMPLETE METHOD WITH DEBUGGING
     */
    public function initiatePayment($transaction)
    {
        // 🔍 DEBUG: Log entry point
        Log::debug('[FedaPayService] initiatePayment() called', [
            'transaction_id' => $transaction->transaction_id ?? 'N/A',
            'user_id' => $transaction->user_id ?? 'N/A',
        ]);

        try {
            // ✅ Amount in smallest unit (XOF has no decimals)
            $amount = (int) round($transaction->amount);

            // ✅ Format phone: remove +, ensure Benin format
            $phone = $this->formatPhoneNumber($transaction->phone_number);

            // 🔍 DEBUG: Log request preparation
            Log::info('[FedaPayService] Preparing payment request', [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $amount,
                'phone' => $phone,
                'method' => $transaction->payment_method,
                'base_url' => $this->baseUrl,
            ]);

            // ✅ Build payload according to FedaPay API v1 specification
            $payload = [
                // ✅ REQUIRED: description field
                'description' => 'Abonnement CuniApp - ' . $transaction->transaction_id,
                'amount' => $amount,
                'currency' => ['iso' => 'XOF'],
                'reference' => $transaction->transaction_id,
                'callback_url' => route('payment.callback', ['provider' => 'fedapay'], true),
                'return_url' => route('subscription.status', [], true),
                // ✅ Customer object - simplified structure
                'customer' => [
                    'email' => $transaction->user->email,
                    'firstname' => explode(' ', $transaction->user->name)[0] ?? $transaction->user->name,
                    'lastname' => implode(' ', array_slice(explode(' ', $transaction->user->name), 1)) ?? '',
                    'phone_number' => [
                        'number' => preg_replace('/^\+?229/', '', $phone), // Send without +229 prefix
                        'country' => 'bj',
                    ],
                ],
                // ✅ Payment method mapping
                'payment_method' => $this->getFedaPayMethod($transaction->payment_method),
            ];

            // 🔍 DEBUG: Log the exact payload being sent (without sensitive data)
            $debugPayload = $payload;
            if (isset($debugPayload['customer']['phone_number'])) {
                $debugPayload['customer']['phone_number']['number'] = '***MASKED***';
            }
            Log::debug('[FedaPayService] Request payload', [
                'payload' => $debugPayload,
            ]);

            // ✅ Make HTTP request to FedaPay API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/v1/transactions', $payload);

            // 🔍 DEBUG: Log raw response details
            Log::info('[FedaPayService] FedaPay API response received', [
                'status_code' => $response->status(),
                'response_body_preview' => substr($response->body(), 0, 500),
                'response_headers' => $response->headers(),
                'transaction_id' => $transaction->transaction_id,
            ]);

            // ✅ Handle successful HTTP response (200-299)
            if ($response->successful()) {
                $data = $response->json();

                // 🔍 DEBUG: Log parsed JSON structure for debugging
                Log::debug('[FedaPayService] Parsed response structure', [
                    'response_keys' => array_keys($data ?? []),
                    'has_transaction' => isset($data['transaction']),
                    'has_v1_transaction' => isset($data['v1/transaction']),
                    'has_url' => isset($data['url']),
                    'has_checkout_url' => isset($data['checkout_url']),
                ]);

                // 🔍 FIX: FedaPay API v1 returns data under 'v1/transaction' key
                // Check multiple possible locations for the checkout URL
                $checkoutUrl = null;
                $fedapayTransactionId = null;

                // Option 1: API v1 format - nested under 'v1/transaction'
                if (isset($data['v1/transaction']['payment_url'])) {
                    $checkoutUrl = $data['v1/transaction']['payment_url'];
                    $fedapayTransactionId = $data['v1/transaction']['id'] ?? null;
                    Log::info('[FedaPayService] Found checkout_url in v1/transaction.payment_url');
                }
                // Option 2: Standard format - transaction.url
                elseif (isset($data['transaction']['url'])) {
                    $checkoutUrl = $data['transaction']['url'];
                    $fedapayTransactionId = $data['transaction']['id'] ?? null;
                    Log::info('[FedaPayService] Found checkout_url in transaction.url');
                }
                // Option 3: Direct url field
                elseif (isset($data['url'])) {
                    $checkoutUrl = $data['url'];
                    $fedapayTransactionId = $data['id'] ?? null;
                    Log::info('[FedaPayService] Found checkout_url in root url');
                }
                // Option 4: checkout_url field
                elseif (isset($data['checkout_url'])) {
                    $checkoutUrl = $data['checkout_url'];
                    $fedapayTransactionId = $data['id'] ?? null;
                    Log::info('[FedaPayService] Found checkout_url in root checkout_url');
                }
                // Option 5: payment_url at root level
                elseif (isset($data['payment_url'])) {
                    $checkoutUrl = $data['payment_url'];
                    $fedapayTransactionId = $data['id'] ?? null;
                    Log::info('[FedaPayService] Found checkout_url in root payment_url');
                }

                // ✅ If we found a valid checkout URL, return success
                if ($checkoutUrl) {
                    Log::info('[FedaPayService] Payment initiation SUCCESS', [
                        'transaction_id' => $transaction->transaction_id,
                        'fedapay_transaction_id' => $fedapayTransactionId,
                        'checkout_url' => $checkoutUrl,
                    ]);

                    return [
                        'success' => true,
                        'checkout_url' => $checkoutUrl,
                        'fedapay_transaction_id' => $fedapayTransactionId,
                        'raw_response' => $data, // Include full response for debugging if needed
                    ];
                }

                // 🔍 DEBUG: If no checkout URL found, log the full response for analysis
                Log::error('[FedaPayService] No checkout_url found in successful response', [
                    'transaction_id' => $transaction->transaction_id,
                    'full_response' => $data,
                    'response_keys' => array_keys($data ?? []),
                ]);

                return [
                    'success' => false,
                    'error' => 'FedaPay API returned success but no checkout URL found. Check response structure.',
                    'response' => $data,
                    'debug_info' => [
                        'response_keys' => array_keys($data ?? []),
                        'suggestion' => 'FedaPay API v1 may return data under "v1/transaction" key',
                    ],
                ];
            }

            // ❌ Handle HTTP error responses (4xx, 5xx)
            Log::error('[FedaPayService] FedaPay payment initiation FAILED (HTTP error)', [
                'status' => $response->status(),
                'response_body' => $response->json() ?? $response->body(),
                'request_payload' => $debugPayload,
                'headers_sent' => [
                    'Authorization' => 'Bearer ***',
                    'Content-Type' => 'application/json',
                    'X-Request-ID' => $response->header('X-Request-Id') ?? 'N/A',
                ],
                'transaction_id' => $transaction->transaction_id,
            ]);

            return [
                'success' => false,
                'error' => 'FedaPay API Error (HTTP ' . $response->status() . '): ' .
                    ($response->json('error') ?? $response->json('message') ?? 'Unknown error'),
                'response' => $response->json() ?? ['raw_body' => $response->body()],
                'http_status' => $response->status(),
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // 🔍 DEBUG: Network/connection errors
            Log::error('[FedaPayService] Connection error to FedaPay API', [
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
                'transaction_id' => $transaction->transaction_id ?? 'N/A',
                'base_url' => $this->baseUrl,
            ]);

            return [
                'success' => false,
                'error' => 'Connection error: Unable to reach FedaPay API. Please check your internet connection.',
                'error_code' => 'CONNECTION_ERROR',
            ];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // 🔍 DEBUG: Request-level errors (timeouts, etc.)
            Log::error('[FedaPayService] Request exception', [
                'message' => $e->getMessage(),
                'response' => $e->response?->body() ?? 'No response body',
                'transaction_id' => $transaction->transaction_id ?? 'N/A',
            ]);

            return [
                'success' => false,
                'error' => 'Request error: ' . $e->getMessage(),
                'error_code' => 'REQUEST_EXCEPTION',
            ];
        } catch (\Exception $e) {
            // 🔍 DEBUG: Catch-all for unexpected errors
            Log::error('[FedaPayService] Unexpected exception', [
                'class' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
                'transaction_id' => $transaction->transaction_id ?? 'N/A',
            ]);

            return [
                'success' => false,
                'error' => 'Unexpected error: ' . $e->getMessage(),
                'error_code' => 'UNEXPECTED_ERROR',
            ];
        }
    }

    /**
     * Verify transaction status via API
     */
    public function verifyTransaction($fedapayId)
    {
        try {
            Log::info('[FedaPayService] Verifying transaction', [
                'fedapay_id' => $fedapayId,
                'base_url' => $this->baseUrl,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/v1/transactions/' . $fedapayId);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning('[FedaPayService] Transaction verification failed', [
                'fedapay_id' => $fedapayId,
                'status' => $response->status(),
            ]);

            return ['success' => false, 'error' => 'Not found (HTTP ' . $response->status() . ')'];
        } catch (\Exception $e) {
            Log::error('[FedaPayService] Verification exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature($payload, $signature, $secret)
    {
        // FedaPay signature format: t=TIMESTAMP,s=SIGNATURE
        parse_str(parse_url('http://x?' . $signature, PHP_URL_QUERY), $parts);

        if (!isset($parts['t']) || !isset($parts['s'])) {
            Log::warning('[FedaPayService] Webhook signature missing components', [
                'signature_preview' => substr($signature ?? '', 0, 50),
            ]);
            return false;
        }

        $timestamp = $parts['t'];
        $expectedSignature = $parts['s'];

        // Reject if timestamp is too old (>5 minutes)
        if (abs(time() - $timestamp) > 300) {
            Log::warning('[FedaPayService] Webhook timestamp too old', [
                'timestamp' => $timestamp,
                'current_time' => time(),
                'diff' => abs(time() - $timestamp),
            ]);
            return false;
        }

        // Compute expected signature: HMAC-SHA256 of "timestamp.payload"
        $signedPayload = $timestamp . '.' . $payload;
        $computedSignature = hash_hmac('sha256', $signedPayload, $secret);

        $isValid = hash_equals($computedSignature, $expectedSignature);

        Log::info('[FedaPayService] Webhook signature verification', [
            'is_valid' => $isValid,
            'timestamp' => $timestamp,
        ]);

        return $isValid;
    }

    /**
     * Map our payment method to FedaPay's method code
     */
    private function getFedaPayMethod($method)
    {
        return match ($method) {
            'momo' => 'mtn_bj',
            'moov' => 'moov_bj',
            'celtis' => 'celtis_bj',
            default => 'mtn_bj',
        };
    }

    /**
     * Format phone number for Benin (+229XXXXXXXXX)
     */
    private function formatPhoneNumber($phone)
    {
        if (!$phone) return null;

        // Remove all non-digit except +
        $clean = preg_replace('/[^\d+]/', '', $phone);

        // Already international
        if (str_starts_with($clean, '+229')) {
            return $clean;
        }

        // Has 229 prefix but no +
        if (str_starts_with($clean, '229') && strlen($clean) === 11) {
            return '+' . $clean;
        }

        // Local Benin format: 01XXXXXXXX (10 digits)
        if (preg_match('/^01\d{8}$/', $clean)) {
            return '+229' . substr($clean, 1);
        }

        // Fallback: extract last 8 digits and prefix
        $digits = preg_replace('/\D/', '', $clean);
        if (strlen($digits) >= 8) {
            return '+229' . substr($digits, -8);
        }

        return $clean; // Return as-is if unsure
    }
}
