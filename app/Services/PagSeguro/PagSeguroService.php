<?php
namespace App\Services;

use GuzzleHttp\Client;

class PagSeguroService
{
    private $baseUrl;
    private $token;

    public function __construct()
    {
        $this->baseUrl = env('PAGSEGURO_API_URL');
        $this->token = env('PAGSEGURO_TOKEN');
    }

    public function generateQrCode($referenceId)
    {
        $client = new Client();
        $response = $client->post("{$this->baseUrl}/instant-payments/qrcodes", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'reference_id' => $referenceId,
                'amount' => [
                    'value' => 0,
                ],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}