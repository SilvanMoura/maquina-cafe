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
                'reference_id' => "{$referenceId}",
                'customer' => [
                    'name' => 'Silvan Moura',
                    'email' => 'silvancortes6537@gmail.com',
                    'tax_id' => '12345678909',
                    'phones' => [
                        [
                            'country' => '55',
                            'area' => '53',
                            'number' => '999999999',
                            'type' => 'MOBILE'
                        ]
                    ]
                ],
                'qr_codes' => [
                    [
                        'expiration_date' => '2025-06-04T23:59:59.000-03:00',
                        'amount' => [
                            'value' => 0
                        ]
                    ]
                ],
                'notification_urls' => [
                    "https://0880-2804-14d-403a-8011-db78-4509-91af-c336.ngrok-free.app/notifications"
                ]
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        // Passa o link QR code diretamente para a view
        $qrCodeLink = $responseBody['qr_codes'][0]['links'];
        

        return $qrCodeLink;

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
