<?php

namespace App\Http\Controllers;

use App\Services\StoreService;
use App\Services\ModuleService;
use App\Services\MQTTService;
use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

        $allPixRefunded = $this->storeService->getAllPixRefunded();

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
        //return $this->storeService->getLatestRefunds();
        // --- ENVIA PARA A VIEW ---
            return view('dashboard', compact(
                'storesCount',
                //'posCount',
                'allPixRefunded',
                'allPix',
                'todaySales',
                'todayCount',
                'countOnline'
            ));
    }

    public function usuariosView(){
        $allUsers = $this->storeService->getUsers();
        return view('users', compact('allUsers'));
    }

    public function createUser(Request $request)
    {
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make('123456'),
            'level' => 3,
        ]);

        return response()->json(['message' => 'Usuário registrado com sucesso'], 201);
    }

    public function updateUsers(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->level = $request->input('level');

        $user->save();

        return response()->json(['message' => 'Usuário alterado com sucesso'], 201);
    }

    public function deleteUsers(Request $request){
        $user = User::findOrFail($request->input('id'));
        $user->delete();

        return response()->json(['message' => 'Usuário excluido com sucesso'], 201);
    }

    public function perfilView(){
        $userId = Auth::id();
        $user = User::select('*')->where('id', $userId)->first();

        return view('account', ['user' => $user]);
    }

    public function newPassword(Request $request, $id){
        $user = User::findOrFail($id);
        $user->password = $request->input('novaSenha');
        $user->save();

        return response()->json(['message' => 'Senha atualizada com sucesso'], 201);
    }
}
