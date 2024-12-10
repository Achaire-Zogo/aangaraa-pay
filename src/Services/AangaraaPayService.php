<?php

namespace Aangaraa\Pay\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Aangaraa\Pay\Models\Transaction;

class AangaraaPayService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('aangaraa-pay');
    }

    public function initializePayment($data)
    {
        try {
            $this->validatePaymentData($data);

            $transaction = Transaction::create([
                'amount' => $data['amount'],
                'phone_number' => $data['phone_number'],
                'description' => $data['description'],
                'app_key' => env('AANGARAA_PAY_APP_KEY'), // Utilisation de la variable d'environnement
                'transaction_id' => $data['transaction_id'],
                'operator' => $data['operator'],
                'status' => 'PENDING',
                'currency' => $data['currency'] ?? 'XAF',
            ]);

            return $this->processPayment($transaction, $data);
        } catch (\Exception $e) {
            Log::error('AangaraaPay Payment Initialization Error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function processPayment($transaction, $data)
    {
        $appKey = env('AANGARAA_PAY_APP_KEY'); // Utilisation de la variable d'environnement
        $payload = [
            'phone_number' => $data['phone_number'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'app_key' => $appKey,
            'transaction_id' => $transaction->transaction_id,
            'return_url' => $data['return_url'],
            'notify_url' => $data['notify_url'],
            'operator' => $data['operator'],
        ];

        // Appel à votre API pour initier le paiement
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->getAangaraaPayApiUrl() . '/direct_payment', $payload);

        if ($response->successful()) {
            $transaction->update([
                'provider_reference' => $response->json()['reference_id'],
                'status' => 'PROCESSING'
            ]);

            return [
                'status' => 'success',
                'message' => 'Payment initiated successfully',
                'transaction_id' => $transaction->transaction_id,
                'reference' => $transaction->provider_reference
            ];
        }

        throw new \Exception('Payment initialization failed: ' . $response->body());
    }

    public function checkTransactionStatus($payToken)
    {
        try {
            $appKey = env('AANGARAA_PAY_APP_KEY'); // Utilisation de la variable d'environnement
            $payload = [
                'payToken' => $payToken,
                'app_key' => $appKey,
            ];

            // Appel à votre API pour vérifier le statut de la transaction
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->getAangaraaPayApiUrl() . '/aangaraa_check_status', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to retrieve transaction status: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('AangaraaPay Check Status Error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function validatePaymentData($data)
    {
        $required = ['phone_number', 'amount', 'description', 'transaction_id', 'operator'];
        
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        if (!in_array($data['operator'], ['MTN_Cameroon', 'Orange_Cameroon'])) {
            throw new \Exception('Invalid operator');
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new \Exception('Invalid amount');
        }
    }

    protected function getAangaraaPayApiUrl()
    {
        return env('AANGARAA_PAY_API_URL', 'https://your-api-url.com/api'); // Utilisation de la variable d'environnement
    }
}