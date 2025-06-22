<?php
namespace App\Http\Controllers;

use App\Services\StoreService;
use App\Services\MQTTService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function dashboardView()
    {
        // --- PARTE PADRÃO DO DASHBOARD ---
        $storesCount = count($this->storeService->getStores());
        $posCount = count($this->storeService->getPos());
        $allPix = $this->storeService->getAllPix();

        $todaySales = $this->storeService->getPagamentosHoje('hoje');
        $sevenDaysSales = $this->storeService->getPagamentosHoje('7dias');
        $thirtyDaysSales = $this->storeService->getPagamentosHoje('30dias');
        $allSales = $this->storeService->getPagamentosHoje('todos');

        $todayCount = count($todaySales['results']);
        $sevenDaysCount = count($sevenDaysSales['results']);
        $thirtyDaysCount = count($thirtyDaysSales['results']);
        $allCount = count($allSales['results']);

        $todaySales = $this->storeService->valueTotal($todaySales);
        $sevenDaysSales = $this->storeService->valueTotal($sevenDaysSales);
        $thirtyDaysSales = $this->storeService->valueTotal($thirtyDaysSales);
        $allSales = $this->storeService->valueTotal($allSales);

        // --- VERIFICAÇÃO DE MÓDULOS ONLINE VIA MQTT ---
        $mqttService = app(MQTTService::class);
        Cache::forget('online_devices');

        // Envia ping
        $mqttService->connect();
        $mqttService->publish("status/ping", json_encode([
            'ping' => true,
            'timestamp' => now()->toDateTimeString()
        ]));

        // Coleta respostas
        $onlineDevices = [];
        $mqttService->subscribe('status/pong/#', function ($topic, $message) use (&$onlineDevices) {
            $data = json_decode($message, true);
            if (isset($data['deviceID'])) {
                $onlineDevices[$data['deviceID']] = [
                    'idData' => $data['deviceID'] ?? null,
                    'timestamp' => now()->toDateTimeString()
                ];
            }
        });

        // Processa mensagens por 2 segundos
        $mqttService->loopFor(2);
        $mqttService->disconnect();

        // Armazena no cache
        Cache::put('online_devices', $onlineDevices, now()->addSeconds(10));
        return response()->json($onlineDevices); // ou envie para view se quiser
        // --- ENVIA PARA A VIEW ---
        return view('dashboard', compact(
            'storesCount',
            'posCount',
            'allPix',
            'todaySales',
            'sevenDaysSales',
            'thirtyDaysSales',
            'allSales',
            'todayCount',
            'sevenDaysCount',
            'thirtyDaysCount',
            'allCount',
            'onlineDevices'
        ));
    }
}
