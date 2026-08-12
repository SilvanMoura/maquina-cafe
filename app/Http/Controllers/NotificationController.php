<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PixReceipt;
use App\Models\TransferPix;
use App\Services\MQTTService;
use App\Services\StoreService;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Log;
use App\Events\SendCreditNotification;

use Endroid\QrCode\Builder\Builder; // composer require endroid/qr-code
use Endroid\QrCode\Writer\PngWriter;

class NotificationController extends Controller
{
    protected $mqttService;
    private $StoreService;

    public function __construct(MQTTService $mqttService, StoreService $StoreService)
    {
        $this->mqttService = $mqttService;
        $this->StoreService = $StoreService;
    }

    public function handle(Request $request)
    {
        sleep(1);
        $data = $request->all();

        if (isset($data['action']) && $data['action'] === 'payment.created') {

            $idPagamento = $data['data']['id'];

            if (!$idPagamento) {
                Log::warning("Notificação 'payment.created' recebida sem ID.");
                return response()->json(['message' => 'Notificação inválida.'], 400);
            }

            $posData = $this->StoreService->getPaymentById($idPagamento);

            Log::info("chegada: ", $posData);
            $module = new ModuleService();
            $valueModule = $module->getModuloById($posData['external_reference']);
            $storeData = $this->StoreService->getStoreInternalId($posData['pos_id']);
            $deviceID = $posData['external_reference'];

            $alreadyProcessed = PixReceipt::where('id_payment', $posData['id'])->exists();
            if ($alreadyProcessed) {
                Log::warning('Pagamento já processado. Ignorando webhook duplicado.', [
                    'payment_id' => $posData['id'],
                ]);
                $this->StoreService->physicalOrder($posData['store_id'], $deviceID);
                return response()->noContent(200);
            }

            $valueModule = $module->getModuloById($posData['external_reference']);
            $storeData = $this->StoreService->getStoreInternalId($posData['pos_id']);
            $deviceID = $posData['external_reference'];
            $pulsos = $posData['transaction_amount'];

            $isOnline = $this->isDeviceOnlineViaMQTT($valueModule);

            if (!$isOnline) {
                Log::warning("Módulo $valueModule está offline. Iniciando chargeback...");
                //$this->StoreService->physicalOrder($posData['store_id'], $deviceID);
                $reembolso = $this->StoreService->executeChargeback($idPagamento);
                $this->StoreService->physicalOrder($posData['store_id'], $deviceID);

                $transaction = PixReceipt::create([
                    'external_reference'  => $posData['external_reference'] ?? null,
                    'pos_id'              => $posData['pos_id'] ?? null,
                    //'status'              => $posData['status'] ?? null,
                    'store_id'            => $posData['store_id'] ?? null,
                    'transaction_amount'  => isset($posData['transaction_amount']) ? floor($posData['transaction_amount']) : null,
                    'id_payment'          => $posData['id'] ?? null,
                    'transaction_id'      => $posData['transaction_id'],
                    'status'              => 'Estornado - Módulo Offline',
                    'module'              => $deviceID,
                    'id_store_internal'   => $storeData['id'],
                    'id_user_internal'    => $storeData['user'],
                ]);

                //return response()->json(['message' => 'Chargeback realizado por módulo offline.'], 200);
                return response()->noContent(200);
            } else {
                $this->StoreService->physicalOrder($posData['store_id'], $deviceID);
                // Dados a serem enviados ao dispositivo
                $message = json_encode([
                    'pulsos' => $pulsos,
                    'deviceID' => "mccf{$valueModule}",
                    'message' => "pulsos de crédito"
                ]);

                try {
                    $this->mqttService->connect();
                    $this->mqttService->publish("creditos/", $message);
                    $this->mqttService->disconnect();

                    Log::info("Mensagem MQTT publicada para $deviceID: $message");

                    try {
                        $data = $this->StoreService->getPixReceiptPdf($idPagamento);
                        Log::info("Recibo processado com sucesso", ['data' => $data]);
                    } catch (\Throwable $e) {
                        Log::warning("Falha ao processar recibo PIX", [
                            'payment_id' => $idPagamento,
                            'erro' => $e->getMessage()
                        ]);
                    }
                    $transaction = PixReceipt::create([
                        'external_reference'  => $posData['external_reference'] ?? null,
                        'pos_id'              => $posData['pos_id'] ?? null,
                        //'status'              => $posData['status'] ?? null,
                        'store_id'            => $posData['store_id'] ?? null,
                        'valor'  => isset($posData['transaction_amount']) ? floor($posData['transaction_amount']) : null,
                        'id_payment'          => $posData['id'] ?? null,
                        'transaction_id'      => $posData['transaction_id'],
                        'status'          => 'Recebido',
                        'module'            => $deviceID,
                        'id_store_internal' => $storeData['id'],
                        'id_user_internal' => $storeData['user'],
                    ]);
                    Log::info('Transação salva com sucesso', ['transaction' => $transaction]);
                    //return response()->json(['message' => 'Notificação processada com sucesso.'], 200);
                    return response()->noContent(200);
                } catch (\Exception $e) {
                    Log::error("Erro ao processar notificação: " . $e->getMessage());
                    return response()->json(['message' => 'Erro ao processar a notificação.'], 500);
                }
            }
        }
        return response()->noContent(200);
    }

    public function isDeviceOnlineViaMQTT($deviceID)
    {
        $mqttService = app(MQTTService::class);

        $respostaRecebida = false;
        // Envia ping diretamente para o módulo
        Log::info('teste modulo online: ' . $deviceID);
        $mqttService->connect();

        // Escuta somente a resposta deste módulo
        $mqttService->subscribe("status/pong/mccf{$deviceID}", function ($topic, $message) use (&$respostaRecebida, $deviceID) {
            $data = json_decode($message, true);
            if (isset($data['deviceID']) && $data['deviceID'] == "mccf{$deviceID}") {
                $respostaRecebida = true;
            }
        });

        $mqttService->publish("status/ping", json_encode([
            'ping' => true,
            'timestamp' => now()->toDateTimeString()
        ]));

        // Aguarda resposta por até 2 segundos
        $mqttService->loopFor(2);
        $mqttService->disconnect();

        return $respostaRecebida;
    }
}
