<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Module;
use Illuminate\Support\Facades\Http;

class ModuleService
{
    private $baseUrl;
    private $token;

    public function __construct(){}

    public function getModules()
    {
        return Module::all();
    }

    public function newModule($module)
    {

        $response = Module::create([
            'codigo'   => $module
        ]); 

        $responseBody = json_decode($response, true);

        return $responseBody;

    }
}
