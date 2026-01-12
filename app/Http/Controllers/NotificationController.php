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

    /* public function handleAlter()
    {
        $responseBody = $this->StoreService->getFullExtract();
        
        $pixRecebido = array_filter($responseBody, function ($transacao) {
            return isset($transacao['titulo']) && $transacao['titulo'] === 'Pix recebido';
        });

        $pixRecebido = array_values($pixRecebido);

        $idTransacoes = array_map(fn($pix) => $pix['idTransacao'], $pixRecebido);

        // Consulta o banco para pegar todos os que já existem
        $registrados = PixReceipt::whereIn('idTransacao', $idTransacoes)
            ->pluck('idTransacao')
            ->toArray();

        // Filtra apenas os que ainda não estão no banco
        $novosPix = array_filter($pixRecebido, function ($pix) use ($registrados) {
            return !in_array($pix['idTransacao'], $registrados);
        });

        // Resetar índices
        $novosPix = array_values($novosPix);
        //return $novosPix;
        // Salvar no banco apenas os campos necessários
        foreach ($novosPix as $pix) {
            $deviceID = $pix['detalhes']['txId'];
            $deviceID = preg_replace('/^(mccf)(\d+)$/', '$1$2', $deviceID);
            
            $pulsos = floor($pix['valor'] / 1);
            
            // Verifica se módulo está online
            $isOnline = $this->isDeviceOnlineViaMQTT($deviceID);

            if ($isOnline) {
                // Monta mensagem para enviar ao módulo
                $message = json_encode([
                    'pulsos'   => $pulsos,
                    'deviceID' => $deviceID,
                    'message'  => "pulsos de crédito"
                ]);

                // Envia para o módulo via MQTT
                $this->mqttService->connect();
                $this->mqttService->publish("creditos/", $message);
                $this->mqttService->disconnect();

                // Cria registro normal do Pix
                PixReceipt::create([
                    'idTransacao'        => $pix['idTransacao'],
                    'tipoTransacao'      => $pix['tipoTransacao'],
                    'valor'              => $pix['valor'],
                    'titulo'             => $pix['titulo'],
                    'txId'               => $pix['detalhes']['txId'] ?? null,
                    'nomePagador'        => $pix['detalhes']['nomePagador'] ?? null,
                    'cpfCnpjPagador'     => $pix['detalhes']['cpfCnpjPagador'] ?? null,
                    'nomeEmpresaPagador' => $pix['detalhes']['nomeEmpresaPagador'] ?? null,
                    'numeroDocumento'    => $pix['numeroDocumento'] ?? null,
                    'endToEndId'         => $pix['detalhes']['endToEndId'] ?? null,
                    'status'             => 'Recebido',
                    'dataTransacao'      => $pix['dataTransacao'],
                ]);
            } else{
                PixReceipt::create([
                    'idTransacao'        => $pix['idTransacao'],
                    'tipoTransacao'      => $pix['tipoTransacao'],
                    'valor'              => $pix['valor'],
                    'titulo'             => $pix['titulo'],
                    'txId'               => $pix['detalhes']['txId'] ?? null,
                    'nomePagador'        => $pix['detalhes']['nomePagador'] ?? null,
                    'cpfCnpjPagador'     => $pix['detalhes']['cpfCnpjPagador'] ?? null,
                    'nomeEmpresaPagador' => $pix['detalhes']['nomeEmpresaPagador'] ?? null,
                    'numeroDocumento'    => $pix['numeroDocumento'] ?? null,
                    'endToEndId'         => $pix['detalhes']['endToEndId'] ?? null,
                    'status'             => 'Módulo Offline',
                    'dataTransacao'      => $pix['dataTransacao'],
                ]);
            }
        }


        // Quantos são novos
        $qtdNovos = count($novosPix);

        return [
            'quantidade_novos' => $qtdNovos,
            'novos_pix' => $novosPix
        ];
        return response()->json(['message' => 'Notificação processada com sucesso.'], 200);
    } */

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
            Log::info("ponto1");
            $storeData = $this->StoreService->getStoreInternalId($posData['pos_id']);
            Log::info("ponto2");
            $deviceID = $posData['external_reference'];
            $pulsos = $posData['transaction_amount'];

            $isOnline = $this->isDeviceOnlineViaMQTT($valueModule);
            Log::info("ponto3");
            sleep(2);
            if (!$isOnline) {
                Log::warning("Módulo $valueModule está offline. Iniciando chargeback...");
                $this->StoreService->physicalOrder($posData['store_id'], $deviceID);
                $reembolso = $this->StoreService->executeChargeback($idPagamento);
                //Log::info("Chargeback executado: ", [$reembolso]);
                //$this->StoreService->physicalOrder($posData['store_id'], $deviceID);

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

                return response()->json(['message' => 'Chargeback realizado por módulo offline.'], 200);
            }
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
                
                $data = $this->StoreService->getPixReceiptPdf($idPagamento);
                Log::info("Recibo: ". $data);
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

                //                                                                                                                                                                                                              Log::info('Transação salva com sucesso', ['transaction' => $transaction]);
                return response()->json(['message' => 'Notificação processada com sucesso.'], 200);
            } catch (\Exception $e) {
                Log::error("Erro ao processar notificação: " . $e->getMessage());
                return response()->json(['message' => 'Erro ao processar a notificação.'], 500);
            }
        } else {
            /* $idPagamento = $data['data']['id'] ?? null;
            $posData = $this->StoreService->getPaymentById($idPagamento);
            $deviceID = $posData['external_reference'];

            $this->StoreService->physicalOrder($posData['store_id'], $deviceID);
            $this->StoreService->executeChargeback($idPagamento); */
            //Log::info("Notificação recebida: ", $data);
        }

        //return response()->json(['message' => 'Tipo de notificação não suportado.'], 200);
    }

    public function isDeviceOnlineViaMQTT($deviceID)
    {
        $mqttService = app(MQTTService::class);

        $respostaRecebida = false;
        Log::info("ponto4");
        // Envia ping diretamente para o módulo
        Log::info('modulo: ' . $deviceID);
        $mqttService->connect();
        $mqttService->publish("status/ping", json_encode([
            'ping' => true,
            'timestamp' => now()->toDateTimeString()
        ]));
        
        // Escuta somente a resposta deste módulo
        $mqttService->subscribe("status/pong/mccf{$deviceID}", function ($topic, $message) use (&$respostaRecebida, $deviceID) {
            $data = json_decode($message, true);
            if (isset($data['deviceID']) && $data['deviceID'] == "mccf{$deviceID}") {
                $respostaRecebida = true;
            }
        });

        // Aguarda resposta por até 2 segundos
        $mqttService->loopFor(2);
        $mqttService->disconnect();

        return $respostaRecebida;
    }
/* 
    public function inter(Request $request)
    {
        Log::warning("notificação: " . $request);
        return response()->json(['message' => 'Notificação recebida'], 200);
    } */

    /* public function gerarQr()
    {
        // -------------- 1) DADOS (substitua pelos seus) ----------------
        $pixKey      = 'c4e35d0c-8fc3-416e-832e-0b01af465640'; // sua chave Pix (CNPJ/EVP/email/tel)
        $txid        = 'mccf0002'; // id da máquina (<=25 chars)
        $merchantName = 'Comercial Colonial'; // até ~25 chars
        $merchantCity = 'Pelotas'; // até ~15 chars
        // ----------------------------------------------------------------

        // Função helper para montar tag TT + LL + value
        $buildTL = function ($tag, $value) {
            $len = strlen($value);
            return sprintf('%02s%02d%s', $tag, $len, $value);
        };

        // -------------- 2) Montar Merchant Account Info (tag 26) ----------
        $gui  = $buildTL('00', 'BR.GOV.BCB.PIX');        // GUI
        $key  = $buildTL('01', $pixKey);                // Chave Pix
        // se quiser adicionar descrição fixa dentro do 26:
        // $desc = $buildTL('02', 'MACHINE QR');
        $merchantAccountInfo = $gui . $key; // . $desc (se usar)
        $tag26 = $buildTL('26', $merchantAccountInfo);

        // -------------- 3) Campos fixos ----------------------------------
        $payload  = '';
        $payload .= $buildTL('00', '01');          // Payload Format Indicator
        // Optionally: $payload .= $buildTL('01', '11'); // point of initiation (11 static)
        $payload .= $tag26;
        $payload .= $buildTL('52', '0000');       // Merchant Category Code
        $payload .= $buildTL('53', '986');        // Currency BRL
        // omit 54 (valor) -> permite pagador digitar valor
        $payload .= $buildTL('58', 'BR');         // Country
        $payload .= $buildTL('59', $merchantName); // Merchant name
        $payload .= $buildTL('60', $merchantCity); // Merchant city

        // Additional Data Field Template (tag 62) -> subtag 05 = txid
        $add05 = $buildTL('05', $txid);
        $tag62 = $buildTL('62', $add05);
        $payload .= $tag62;

        // -------------- 4) CRC16 (tag 63 é calculada) ---------------------
        // Para calcular, adicionamos '6304' e rodamos CRC16-CCITT (poly 0x1021, init 0xFFFF)
        $payloadToCrc = $payload . '6304';
        $crc = $this->crc16_ccitt($payloadToCrc);
        // append CRC (hex uppercase)
        $payload .= '63' . '04' . $crc;

        // $payload agora é a string EMV completa (BR Code)
        // -------------- 5) Gerar imagem QR (usando endroid/qr-code) ------
        // composer require endroid/qr-code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($payload)
            ->size(400)
            ->margin(10)
            ->build();

        $data = $result->getString();
        return response($data)->header('Content-Type', 'image/png');
    }

    public function crc16_ccitt($data)
    {
        $crc = 0xFFFF;
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        return strtoupper(sprintf("%04X", $crc));
    } */
}
