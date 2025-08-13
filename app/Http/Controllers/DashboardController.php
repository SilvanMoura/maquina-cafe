<?php

namespace App\Http\Controllers;

use App\Services\StoreService;
use App\Services\ModuleService;
use App\Services\MQTTService;
use App\Models\Module;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private $storeService;
    private $moduleService;

    public function __construct(StoreService $storeService, ModuleService $moduleService)
    {
        $this->storeService = $storeService;
        $this->moduleService = $moduleService;
    }

    public function dashboardView()
    {
        // --- PARTE PADRÃO DO DASHBOARD ---
        $storesCount = count($this->storeService->getStores());
        //$posCount = count($this->storeService->getPos());
        $allPix = $this->storeService->getAllPix();

        $todaySales = $this->storeService->getPagamentosHoje();

        $todayCount = count($todaySales['results']);

        $todaySales = $this->storeService->valueTotal($todaySales);

        // --- VERIFICAÇÃO DE MÓDULOS ONLINE VIA MQTT ---
        $mqttService = app(MQTTService::class);
        Cache::forget('online_devices');

        // 3. Envia ping para todos os dispositivos
        $mqttService->connect();
        $mqttService->publish("status/ping", json_encode([
            'ping' => true,
            'timestamp' => now()->toDateTimeString()
        ]));

        // 4. Coleta respostas dos dispositivos
        $onlineDevices = [];
        $mqttService->subscribe('status/pong/#', function ($topic, $message) use (&$onlineDevices) {
            $data = json_decode($message, true);
            if (isset($data['deviceID'])) {
                $onlineDevices[$data['deviceID']] = [
                    'idData' => $data['deviceID'] ?? null,
                    'mac' => $data['mac'] ?? null,
                    'sinal' => $data['rssi'] ?? null,
                    'timestamp' => now()->toDateTimeString()
                ];
            }
        });

        // 5. Processa respostas por até 2 segundos
        $mqttService->loopFor(2);
        $mqttService->disconnect();
        //return $onlineDevices;
        // 6. Armazena dispositivos online no cache por 10 segundos
        $countOnline = count($onlineDevices);
        Cache::put('online_devices', $onlineDevices, now()->addSeconds(10));

        // 7. (Opcional) Marca todos como offline antes de atualizar os que responderam
        $this->moduleService->moduleUpdateAllOffline();

        // 8. Atualiza ou insere dados no banco, incluindo qualidade do sinal
        foreach ($onlineDevices as $deviceID => $info) {
            $cleanId = str_replace('mccf-', '', $deviceID);
            $rssi = $info['sinal'] ?? null;

            if ($rssi >= -50 && $rssi <= -30) {
                $qualidade = 'Sinal Forte';
            } elseif ($rssi > -70 && $rssi < -50) {
                $qualidade = 'Sinal Moderado';
            } elseif ($rssi >= -80 && $rssi <= -70) {
                $qualidade = 'Sinal Fraco';
            } elseif ($rssi < -80) {
                $qualidade = 'Sinal Muito Fraco';
            } else {
                $qualidade = 'Indefinido';
            }

            Module::where('modulo', $cleanId)->update([
                'rssi' => $rssi,
                'sinal_qualidade' => $qualidade,
                'ultima_conexao' => now(),
                'status_online' => true
            ]);

        }
        // --- ENVIA PARA A VIEW ---
            return view('dashboard', compact(
                'storesCount',
                //'posCount',
                'allPix',
                'todaySales',
                'todayCount',
                'countOnline'
            ));
    }
}
