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
        // ✅ Remplacer par
        $this->publicKey = env('FEDAPAY_PUBLIC_KEY')
            ?? config('services.fedapay.public_key')
            ?? Setting::get('fedapay_public_key');

        $this->secretKey = env('FEDAPAY_SECRET_KEY')
            ?? config('services.fedapay.secret_key')
            ?? Setting::get('fedapay_secret_key');

        $this->environment = env('FEDAPAY_ENVIRONMENT', 'sandbox')
            ?? config('services.fedapay.environment')
            ?? Setting::get('fedapay_environment', 'sandbox');

        // URL de base - plus simple
        $this->baseUrl = env('FEDAPAY_BASE_URL')
            ?? ($this->environment === 'production'
                ? 'https://api.fedapay.com'
                : 'https://sandbox-api.fedapay.com');

        // Log configuration for debugging
        Log::info('FedaPay config loaded', [
            'public_key_set' => !empty($this->publicKey),
            'secret_key_set' => !empty($this->secretKey),
            'environment' => $this->environment,
            'base_url' => $this->baseUrl,  // Ajout pour debug
        ]);
    }

    /**
     * ✅ INITIATE PAYMENT WITH FEDAPAY
     */
    public function initiatePayment($transaction)
    {
        try {
            // ✅ Convertir proprement en centimes
            $amountInCents = (int) round(floatval($transaction->amount) * 100);

            Log::info('Initiating FedaPay payment', [
                'transaction_id' => $transaction->transaction_id,
                'amount_original' => $transaction->amount,
                'amount_cents' => $amountInCents,  // ← Debug utile
                'phone' => $transaction->phone_number,
                'method' => $transaction->payment_method,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'CuniApp/1.0 Laravel/' . app()->version(),
            ])->post($this->baseUrl . '/v1/transactions', [
                'amount' => $amountInCents,  // ← Ex: 750000 pour 7500 FCFA
                'currency' => 'XOF',
                'description' => 'Abonnement CuniApp Élevage',
                'reference' => $transaction->transaction_id,
                'callback_url' => route('payment.callback', ['provider' => 'fedapay']),
                'return_url' => route('subscription.status'),
                'customer' => [
                    'email' => $transaction->user->email,
                    'name' => $transaction->user->name,
                    'phone_number' => $this->formatPhoneNumber($transaction->phone_number),
                ],
                'settings' => [
                    'methods' => [$this->getFedaPayMethod($transaction->payment_method)],
                ],
            ], [
                'connect_timeout' => 30,
                'timeout' => 60,
            ]);

            Log::info('FedaPay API response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'checkout_url' => $data['transaction']['url'] ?? $data['url'] ?? null,
                    'transaction_id' => $data['transaction']['id'] ?? $data['id'] ?? null,
                    'response' => $data,
                ];
            }

            Log::error('FedaPay payment failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Erreur FedaPay: ' . $response->status(),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('FedaPay payment initiation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => 'Erreur de connexion à FedaPay: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * ✅ VERIFY TRANSACTION STATUS
     */
    public function verifyTransaction($fedapayTransactionId)
    {
        try {
            // ✅ Also remove /v1/ here
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/v1/transactions/' . $fedapayTransactionId);

            if ($response->successful()) {
                $jsonData = $response->json();
                return [
                    'success' => true,
                    'data' => $jsonData,
                ];
            }

            return [
                'success' => false,
                'error' => 'Transaction not found (HTTP ' . $response->status() . ')',
            ];
        } catch (\Exception $e) {
            Log::error('FedaPay verification failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * ✅ GET FEDAPAY METHOD CODE
     */
    private function getFedaPayMethod($paymentMethod)
    {
        return match ($paymentMethod) {
            'momo' => 'mtn_bj',
            'moov' => 'moov_bj',
            'celtis' => 'celtis_bj',
            default => 'mtn_bj',
        };
    }

    private function formatPhoneNumber($phone)
    {
        $cleaned = preg_replace('/[\s\-]/', '', $phone);

        if (strpos($cleaned, '+229') === 0) {
            return $cleaned;
        }
        if (strpos($cleaned, '229') === 0) {
            return '+' . $cleaned;
        }
        if (preg_match('/^01\d{8}$/', $cleaned)) {
            return '+229' . substr($cleaned, 1);
        }

        // Fallback : ajouter +229 si ce n'est pas déjà fait
        return '+229' . preg_replace('/^0+/', '', $cleaned);
    }
}
