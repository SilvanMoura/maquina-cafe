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
        $response = $client->post("{$this->baseUrl}/orders", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'reference_id' => $referenceId,
                'customer' => [
                    'name' => 'Silvan Moura',
                    'email' => 'silvancortes6537@gmail.com',
                    'tax_id' => '12345678909',
                    'phones' => [
                        [
                            'country' => '55',
                            'area' => '53',
                            'number' => '991674532',
                            'type' => 'MOBILE'
                        ]
                    ]
                ]
                        ],
            'notification_urls' => [
                "https://4829-2804-14d-403a-8011-1132-f2d8-3bd6-1bc2.ngrok-free.app/notifications"
            ]
        ]);

        return json_decode($response->getBody(), true);
    }


    public function getTransactionDetails($transactionId)
    {
        $client = new Client();
        $response = $client->get("{$this->baseUrl}/transactions/{$transactionId}", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
