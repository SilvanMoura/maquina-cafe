<?php

namespace App\Services\PagSeguro;

use Illuminate\Support\Facades\Http;

class PagSeguroService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.pagseguro.base_url');
        $this->token = config('services.pagseguro.token');
    }

    public function createCharge($referenceId, $amount, $pixKey)
    {
        $response = Http::withToken($this->token)->post("{$this->baseUrl}/instant-payments/qrcodes", [
            'reference_id' => $referenceId,
            'amount' => ['value' => $amount],
            'key' => $pixKey,
        ]);

        return $response->json();
    }

    public function getPaymentStatus($chargeId)
    {
        $response = Http::withToken($this->token)->get("{$this->baseUrl}/instant-payments/charges/{$chargeId}");

        return $response->json();
    }
}