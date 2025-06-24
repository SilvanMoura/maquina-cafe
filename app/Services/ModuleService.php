<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Module;
use App\Models\Coupon;
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

    public function getModuloById($id)
    {
        return Module::where('id', $id)->value('modulo');
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

    public function newCoupon($name, $value, $telefone)
    {
        $response = Coupon::create([
            'name'   => $name,
            'value'  => $value,
            'telefone'  => $telefone,
            'status' => 'Ativo'
        ]); 
        $numeroLimpo = preg_replace('/\D/', '', $response['telefone']);
        $linkWhatsapp = "https://wa.me/55{$numeroLimpo}?text=" . urlencode($response->name);
        return $linkWhatsapp;

    }

    public function getCoupons()
    {
        return Coupon::get();
    }
}
