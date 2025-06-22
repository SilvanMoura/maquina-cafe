<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\MQTTService;
use Illuminate\Support\Facades\Log;
use App\Events\SendCreditNotification;

class NotificationController extends Controller
{
    protected $mqttService;

    public function __construct(MQTTService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function handle(Request $request)
    {
        // Captura todos os dados enviados pelo PagSeguro
        $data = $request->all();

        // Define o caminho do arquivo onde os dados serão salvos
        $logFilePath = storage_path('logs/pagseguro_notifications.json');

        $logData = [
            'data' => $data
            /* "apelido" => $data['notification_data']['reference_id'],
            "nome_vendedor" => $data['notification_data']['customer']['name'],
            "id_vendedor" => $data['notification_data']['customer']['tax_id'],
            "id_transacao" => $data['notification_data']['qr_codes'][0]['id'],
            "valor_transacao" => $data['notification_data']['qr_codes'][0]['amount']['value'] / 100,
            "status_transacao" => $data['notification_data']['charges'][0]['status'],
            "nome_comprador" => $data['notification_data']['charges'][0]['payment_method']['pix']['holder']['name'],
            "id_comprador" => $data['notification_data']['charges'][0]['payment_method']['pix']['holder']['tax_id'],
         */];

        try {
            // Lê o conteúdo existente do arquivo JSON, se existir
            if (file_exists($logFilePath)) {
                $existingData = json_decode(file_get_contents($logFilePath), true) ?? [];
                $existingData[] = $logData; // Adiciona os novos dados ao array existente
            } else {
                $existingData = [$logData]; // Cria um novo array com os dados
            }

            // Salva os dados atualizados no arquivo JSON
            file_put_contents($logFilePath, json_encode($existingData, JSON_PRETTY_PRINT));

            // Salva os dados no banco de dados
            //Transaction::create($logData);

            // Envia os dados ao ESP8266 correspondente
            //$deviceId = "mccf-" . $data['reference_id'];
            
            //$this->mqttService->connect();
            //$topic = "creditos/";
            //$message = json_encode(['pulsos' => 5]);
            //$this->mqttService->publish($topic, $message);
            //$this->mqttService->disconnect();

            // Envia os dados ao ESP8266 correspondente
            // Tópico único para o ESP
            //$deviceMAC = "C4:D8:D5:2D:00:63"; // MAC do dispositivo alvo
            $topic = "creditos/";

            // Mensagem com pulsos e deviceID
            $message = json_encode([
                'pulsos' => 5,
                'deviceID' => "mccf-1020", // Deve ser igual ao configurado no ESP
                'message' => "pulsos de crédito"
            ]);

            // Publica a mensagem no broker MQTT
            $this->mqttService->connect();
            Log::info("Conexão com MQTT estabelecida com sucesso.");
            $this->mqttService->publish($topic, $message);
            Log::info("Mensagem publicada no tópico $topic com os dados: $message");
            $this->mqttService->disconnect();
            Log::info("Conexão MQTT encerrada.");
            

            return response()->json(['message' => 'Notificação processada com sucesso.'], 200);
        } catch (\Exception $e) {
            // Em caso de erro, registra no arquivo de log
            $errorLogData = [
                'error' => $e->getMessage(),
                'log_data' => $logData,
                'time' => now()->toDateTimeString(),
            ];

            // Salva o erro no arquivo de log
            $existingErrors = file_exists($logFilePath) ? json_decode(file_get_contents($logFilePath), true) : [];
            $existingErrors[] = $errorLogData;
            file_put_contents($logFilePath, json_encode($existingErrors, JSON_PRETTY_PRINT));

            // Retorna erro ao PagSeguro
            return response()->json(['message' => 'Erro ao processar a notificação.'], 500);
        }
    }

}
