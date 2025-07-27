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

    public function __construct() {}

    public function getModules()
    {
        return Module::where(function ($query) {
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

    public function registerStoreModule($module, $idStore)
    {
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

        $urlComId = "https://74d3-2804-14d-403a-8011-4019-f63b-2c27-f3fc.ngrok-free.app/readCode?id=" . $response->id;
        $linkWhatsapp = "https://wa.me/55{$numeroLimpo}?text=" . urlencode($urlComId);
        return $linkWhatsapp;
    }

    public function getCoupons()
    {
        return Coupon::get();
    }

    public function getCouponsById($couponId)
    {
        return Coupon::where('id', $couponId)->get();
    }

    public function deactivatingCoupon($couponId)
    {
        $response = Coupon::where('id', $couponId)->update([
            'Status' => "Inativo"
        ]);

        return $response;
    }

    public function sendCredits($moduloId, $valor)
    {

        // Conecta ao broker MQTT
        $mqtt = new \App\Services\MQTTService();
        $mqtt->connect();

        // Monta o payload
        $payload = json_encode([
            'message' => 'pulsos de crédito',
            'pulsos' => intval($valor),
            'deviceID' => $moduloId
        ]);

        // Publica no tópico
        $mqtt->publish("creditos/", $payload);

        // Desconecta
        $mqtt->disconnect();

        return [
            'success' => true,
            'message' => "Crédito de $valor enviado para o módulo $moduloId"
        ];
    }

    public function sendCommandToButton($moduloId, $button)
    {
        // Conecta ao MQTT
        $mqtt = app(\App\Services\MQTTService::class);
        $mqtt->connect();

        // Define os tópicos
        $topicComando = "comandos/botao";
        $topicResposta = "resposta/comandos/mccf-{$moduloId}";

        // Prepara o payload com ID padrão "mccf-{$moduloId}"
        $payload = json_encode([
            'message' => 'Acionar botão',
            'botao' => intval($button),
            'deviceID' => "mccf-{$moduloId}"
        ]);

        // Envia o comando
        $mqtt->publish($topicComando, $payload);

        // Variável para capturar a resposta
        $respostaRecebida = false;

        // Inscreve-se no tópico de resposta
        $mqtt->subscribe($topicResposta, function ($topic, $message) use (&$respostaRecebida, $moduloId, $button) {
            $dados = json_decode($message, true);
            
            if (
                isset($dados['deviceID'], $dados['botao'], $dados['status']) &&
                $dados['deviceID'] === "mccf-{$moduloId}" &&
                intval($dados['botao']) === intval($button) &&
                $dados['status'] === 'ok'
            ) {
                $respostaRecebida = true;
            }
        });

        // Processa mensagens por 2 segundos
        $mqtt->loopFor(2);
        $mqtt->disconnect();

        // Retorna conforme o resultado
        if ($respostaRecebida) {
            return [
                'success' => true,
                'message' => "Comando confirmado pelo módulo $moduloId no botão $button"
            ];
        }

        return [
            'success' => false,
            'message' => "Nenhuma confirmação recebida do módulo $moduloId para o botão $button"
        ];
    }

    public function getModulesUse()
    {
        return Module::whereNotNull('idStore')
            ->where('idStore', '!=', '')
            ->get();
    }

    public function moduleUpdateAllOffline(){
        Module::query()->update(['status_online' => 0]);
    }
}
