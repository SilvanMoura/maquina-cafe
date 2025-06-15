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
        return Module::where(function($query) {
            $query->whereNull('idStore')
                ->orWhere('idStore', '');
        })->get();
    }



    public function newModule($module)
    {

        $response = Module::create([
            'modulo'   => $module,
            'idStore'  => ''
        ]); 

        //$responseBody = json_decode($response, true);

        return $response;

    }

    public function registerStoreModule($module, $idStore){
        $response = Module::where('id', $module)->update([
            'idStore' => $idStore
        ]);

        $responseBody = json_decode($response, true);

        return $response;
    }
}
