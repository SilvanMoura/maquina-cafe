<?php

namespace App\Services;

use GuzzleHttp\Client;

class StoreService
{
    private $baseUrl;
    private $token;

    public function __construct()
    {
        $this->baseUrl = "https://api.mercadopago.com/users/2321161890/stores/search";
        $this->token = "APP_USR-7226859123041588-031023-d12365cba1d9c1e218e36c78ae493db2-2321161890";
    }

    public function getStores()
    {
        $client = new Client();
        $response = $client->get("{$this->baseUrl}", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody['results'];

    }
}
