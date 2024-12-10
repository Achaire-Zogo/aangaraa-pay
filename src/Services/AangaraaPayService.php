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
                'app_key' => $data['app_key'],
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
        $appKey = $data['app_key'];
        $payload = [
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'XAF',
            'externalId' => $transaction->transaction_id,
            'payerMessage' => $data['description'],
            'payeeNote' => $data['description'],
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $data['phone_number']
            ],
            'operator' => $data['operator']
        ];

        // Appel à votre API pour initier le paiement
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $appKey
        ])->post($this->getAangaraaPayApiUrl() . '/initiate-payment', $payload);

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

    protected function validatePaymentData($data)
    {
        $required = ['phone_number', 'amount', 'description', 'app_key', 'transaction_id', 'operator'];
        
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
        return env('AANGARAA_PAY_API_URL', 'https://your-api-url.com/api');
    }
}
