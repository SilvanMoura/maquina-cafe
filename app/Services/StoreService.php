<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\User;
use App\Models\PixReceipt;
use App\Models\Store;
use App\Models\Module;
use App\Models\TransferPix;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use FFI;
use GuzzleHttp\Exception\RequestException;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StoreService
{
    private $baseUrl;
    private $token;
    private $idUser;

    public function __construct()
    {
        $this->baseUrl = "https://api.mercadopago.com/users/2710523715/stores/search";
        $this->token = "APP_USR-2071521744281689-092615-03f4c3477efc439c5b0ee19ca1641fe0-2710523715";
        $this->idUser = "2710523715";
    }

    public function getStores()
    {
        $client = new Client();
        $response = $client->get("{$this->baseUrl}", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody['results'];
    }

    public function getStoresById($idStore)
    {
        $client = new Client();
        $response = $client->get("https://api.mercadopago.com/stores/{$idStore}", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody['name'];
    }

    public function getStoresByIdUser($userId)
    {
        return Store::where('user', $userId)->get();
    }

    public function getStoreInternalId($idStore)
    {
        $store = Store::where('idStoreMercadoPago', $idStore)->first(['id', 'user']);

        return $store->toArray();
    }

    public function getPos()
    {
        $client = new Client();
        $response = $client->get("https://api.mercadopago.com/pos", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody['results'];
    }

    public function getPosById($posId)
    {
        $client = new Client();
        $response = $client->get("https://api.mercadopago.com/pos/{$posId}", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function consultOrderinPerson($idUser, $externalPosId)
    {
        $client = new Client();
        $response = $client->get("https://api.mercadopago.com/instore/qr/seller/collectors/{$idUser}/pos/{$externalPosId}/orders", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function newStore($nameStore, $endereco, $complemento, $cidade)
    {

        $client = new Client();

        $response = $client->post("https://api.mercadopago.com/users/{$this->idUser}/stores", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'name' => $nameStore,
                'location' => [
                    'street_number' => $complemento ?? 'S/N',
                    'street_name'   => $endereco,
                    'city_name'     => $cidade,
                    'state_name'    => 'Rio Grande do Sul', // Pode parametrizar se quiser
                    'latitude'      => -31.734942,           // Pode ser dinâmico depois
                    'longitude'     => -52.347392,
                    'reference'     => $nameStore,
                ]
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function newPos($idStore, $nameStore, $moduloValue)
    {

        $client = new Client();

        $response = $client->post("https://api.mercadopago.com/pos", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'store_id' => $idStore,
                'external_id' => "{$moduloValue}",
                'name' => "$nameStore - Caixa",
                'fixed_amount' => false,
                'category' => 5611203
            ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function physicalOrder($idStore, $moduloValue)
    {
        $client = new Client();

        //$moduloValueFormated = str_replace('-', '', $moduloValue);
        //$idStore = 74950966;
        $response = $client->put("https://api.mercadopago.com/instore/qr/seller/collectors/{$this->idUser}/stores/{$idStore}/pos/{$moduloValue}/orders", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'title' => "Pedido do Cliente",
                'description' => "Produto ou serviço escolhido pelo cliente",
                'notification_url' => "https://srv981758.hstgr.cloud/notifications",
                'external_reference' => "$moduloValue",
                "expiration_date" => "2027-12-30T01:30:00.000-03:00",
                'total_amount' => 0,
                'items' => [ // <- agora um array de objetos
                    [
                        'id' => "item1",
                        'title' => "Produto X",
                        'unit_measure' => "unit",
                        'unit_price' => 0.00,
                        'fixed_amount' => true,
                        'quantity' => 1,
                        'total_amount' => 0
                    ]
                ]
            ],
        ]);
        //Log::info("posData1: ". $response);
        $responseBody = json_decode($response->getBody(), true);
        Log::info("posData1: https://api.mercadopago.com/instore/qr/seller/collectors/{$this->idUser}/stores/{$idStore}/pos/{$moduloValue}/orders");
        Log::info("data1: " . $moduloValue);
        return $responseBody;
    }

    public function getPixReceiptPdf($paymentId)
    {
        $client = new Client();

        $response = $client->get(
            "https://www.mercadopago.com.br/money-out/transfer/api/receipt/pix_pdf/{$paymentId}/pix_account/pix_payment.pdf",
            [
                'headers' => [
                    'cookie' => 'ftid=lfgV8PvFoGSSWOpBvcFMRn24tFTxXK1c-1748716757421; cookiesPreferencesNotLogged=%7B%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; p_dsid=da33d370-eecc-4d0c-bdad-d4ba939f5336-1750033492551; _hjSessionUser_492923=eyJpZCI6ImY4MmE3MmIwLWYwNzYtNWIwYy05MWQ0LWVjZGQ4NTliMjI3OCIsImNyZWF0ZWQiOjE3NTAwMzM1NzM5ODIsImV4aXN0aW5nIjp0cnVlfQ==; mp_dx-dev-panel-production-product=true; _hjSessionUser_4992954=eyJpZCI6IjFjNDQ0NTZiLTBhMjYtNWEzMi1iZWY2LTlmZmE5ZTdlMmJkMiIsImNyZWF0ZWQiOjE3NTAwMzM5MjEzNjgsImV4aXN0aW5nIjp0cnVlfQ==; _tt_enable_cookie=1; _ttp=01JZGKKZ7YCQWRDASWKDH1J19Z_.tt.2; orgnickp=DHGCBDEAF12717; orguseridp=2710523715; dxDevPanelAppCollaboratorsModal=true; iaResourcesTooltip=true; mp_spending-tracking_walkthrough={"value":"finished","date":"2026-01-31"}; mp_wsid=eyJhbGciOiJSUzI1NiIsImtpZCI6IlBNREhlSGt2WEdPZ2JmWFNXZ2VnMDRzeEVTZG1yV0N1TndKcFl1N3lGUjg9IiwidHlwIjoiSldUIn0.eyJhZG1pbl9vdHAiOmZhbHNlLCJhdXRoX2Zsb3ciOiJhY2Nlc3MiLCJjbGllbnRfaWQiOjY0OTUyMTMwOTkwNDY1NywiZXhwIjoxODY0NjEwMzQ3LCJpYXQiOjE3Njk5MTU5NDcsImlzcyI6InVybjphdXRoLXNlcnZlcjpzZXNzaW9ucyIsImp0aSI6IjMyNWY3NDQyLTI4MDktNDc1ZS1iMDEzLTBlMzIxYTEzZWRkMiIsInByb2R1Y3RfaWQiOjIsInNpdGVfaWQiOiJtbGIiLCJzc2kiOiJjZjk1YzBiZC00NmM5LTRkMTgtODZmNS1hNWJmYTUwNDA2NmUiLCJzdWIiOiJ1cm46dXNlcnM6MjcxMDUyMzcxNSJ9.ewXGw57O6Fnj5QebA3BL7aB9WLQFAHtDVhz8Go8GtKC8NR6ZtMa9Ssc1R4DxZKJvBRn1Y-BCBte0K_0ejSVJlG57gurzYGBom4SloGCW-clcYCbObkh2mbQ4tSocqpQTm9fE-6C5_1Ob4yeXULpRejPKI-KCjrbsjlrhQi7fPwLJVBh8OEFrL9HgY5QlthgpZUEmEbzwupDEeWf463JtQ9Nnc_pbO00Q2wNIHZR4afKk5WKi92ZSloe7wf_GRvvIAv8gPMVCrX91gT0p7L8iVS6eAyhMOBiUuTy8jC97_JD_yn_0LinUOiL7zchgRarSU22YuNGIimaZv9YI9dnszg; ssid=ghy-013123-Mae3sySAMVqe2FQ3yoJls5v12JCrUQ-__-2710523715-__-1864610347612--RRR_0-RRR_0; orguserid=9Zhd0hd9ZHh09; __rtbh.uid=%7B%22eventType%22%3A%22uid%22%2C%22id%22%3A%222710523715%22%2C%22expiryDate%22%3A%222027-03-18T14%3A54%3A02.700Z%22%7D; __rtbh.lid=%7B%22eventType%22%3A%22lid%22%2C%22id%22%3A%22b8o6zYPxUCCk7wMFFdAT%22%2C%22expiryDate%22%3A%222027-03-18T14%3A54%3A02.701Z%22%7D; _uetvid=f22ee2309feb11f0a11e895521b5b58c; ttcsid_C9SJ5SBC77UADFMAH8T0=1773845642949::dWoAxsPnd7imvLb_NTrf.14.1773845664397.1; ttcsid_CFVSC2JC77U0ARCJTCJ0=1773845643254::hgr4DiL12PZqTL4fD3FJ.14.1773845664406.1; ttcsid=1773845642951::TCVAPm4MmHrKh50lyn-F.15.1773845664406.0; cookiesPreferencesLoggedFallback=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; _ga_XJRJL56B0Z=GS2.1.s1783092152$o44$g0$t1783092152$j60$l0$h0; _d2id=7079c303-33c4-4802-b897-07b5fa130e47; QSI_SI_9QC3cpCiXZYNBQi_intercept=true; _csrf=fzysFmof8ROl7dUXXu3MlZDu; _mldataSessionId=33b4fc5e-f7d3-4951-a12e-d9774637a836; nsa_rotok=eyJhbGciOiJSUzI1NiIsImtpZCI6IjMiLCJ0eXAiOiJKV1QifQ.eyJpZGVudGlmaWVyIjoiY2Y5NWMwYmQtNDZjOS00ZDE4LTg2ZjUtYTViZmE1MDQwNjZlIiwicm90YXRpb25faWQiOiJkNzllODBlMS1jOGE2LTQwNzEtYTM0ZC0yYTk1ZjliYmJlMWQiLCJwbGF0Zm9ybSI6Ik1QIiwicm90YXRpb25fZGF0ZSI6MTc4NjkwNjc1MCwiZXhwIjoxNzg5NDk4MTUwLCJqdGkiOiJlNzM2ZDMyYy1mOTE5LTRmNzUtOTczMy03ZTA1YjYyZjgwMWIiLCJpYXQiOjE3ODY5MDYxNTAsInN1YiI6ImNmOTVjMGJkLTQ2YzktNGQxOC04NmY1LWE1YmZhNTA0MDY2ZSJ9.c81KU6HQHuaQSUu5uWPono1lNhCiHErNdQODZwwH04vdIW4aKj_PWRicmg31-gY6-oDQEiqUByxQX1gtKFFTE20c12yLT92Dv3s3HxOfHyzQX7qF41lnZvlaXhdQ-nah-HC86Drlag4AmSuOK4P4LLzFtRTkZGovbleNFsemTIj4tIkx1igwhuM8fhAWnp7xIxMelQfLrS8V9MSsy9I9c1QmtFHcUVC1mxn5yrpNF6ywS0TuX6ofQL77QaQsKPyyeq77PesvT1P72n7JdQ-zFY_LwZ4MeSqtvOf-7qHm2UWB4OoW1tg6sy9I_iemIpxNzwudxR0oBFguQkQEm6kGlQ; app-theme=yellowblue-light; app-restyled-user=4bb295a70b357f78; app-restyled=true; cookiesPreferencesLogged=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; hide-cookie-banner=2710523715-COOKIE_PREFERENCES_ALREADY_SET; ttl=1786906151780; QSI_HistorySession=https%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1786906098893; p_edsid=9b79f04d-e141-346d-89a8-c2f8c3e957af-1786906154022; x-meli-session-id=armor.13252e2cb34067fa99508a42be913710f007342956e1e6f88423824b6e90cbfa76ba2255f6067e74cb42a50c92622e5c69ed9809868dc8c1c33a5e88a1ff94658f0abd0bf7a5dbd1d07ce54f878c194c45fc71d29939b642bde1f85453e1792c.ed8882dcbb05ceb94041194a96c1fef8',
                    'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X)',
                    'Accept' => 'application/pdf',
                    'Authorization' => "Bearer {$this->token}",
                    'referer' => 'https://www.mercadopago.com.br/',
                    'cache-control' => 'max-age=0',
                    'accept-language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Content-Type' => 'application/json'
                ],
                'stream' => false
            ]
        );

        $directory = storage_path("logs/recibos");
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $pdfPath = $directory . "/recibo_{$paymentId}.pdf";
        file_put_contents($pdfPath, $response->getBody());

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();

        $lines = explode("\n", $text);
        $data = [
            'cpf_remetente' => null,
            'cnpj_remetente' => null,
        ];

        foreach ($lines as $index => $line) {
            $line = trim($line);

            if (Str::contains($line, 'às') && Str::contains($line, 'de')) {
                if (preg_match('/(\d{2}) de (\w+) de (\d{4}), às (\d{2}:\d{2}:\d{2})/', $line, $m)) {
                    $meses = [
                        'janeiro' => '01',
                        'fevereiro' => '02',
                        'março' => '03',
                        'abril' => '04',
                        'maio' => '05',
                        'junho' => '06',
                        'julho' => '07',
                        'agosto' => '08',
                        'setembro' => '09',
                        'outubro' => '10',
                        'novembro' => '11',
                        'dezembro' => '12'
                    ];
                    $mes = $meses[strtolower($m[2])] ?? '00';
                    $data['data_transferencia'] = "{$m[1]}/{$mes}/{$m[3]} {$m[4]}";
                }
            }

            if (Str::startsWith($line, 'R$')) {
                $valor = str_replace(',', '.', preg_replace('/[^\d,]/', '', $line));
                $data['valor'] = (float) $valor;
            }

            if ($line === 'De') {
                $data['nome_remetente'] = trim($lines[$index + 1] ?? '');
            }

            if (Str::startsWith($line, 'Número da transação do Mercado Pago')) {
                $data['id_mercado_pago'] = trim($lines[$index + 1] ?? '');
            }

            if (Str::startsWith($line, 'ID de transação PIX')) {
                $data['id_pix'] = trim($lines[$index + 1] ?? '');
            }
        }

        // Extração robusta de CPF ou CNPJ do texto inteiro
        if (preg_match('/\b\d{3}\.?\d{3}\.?\d{3}-?\d{2}\b/', $text, $m)) {
            $data['cpf_remetente'] = preg_replace('/\D/', '', $m[0]);
        } elseif (preg_match('/\b\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2}\b/', $text, $m)) {
            $data['cnpj_remetente'] = preg_replace('/\D/', '', $m[0]);
        }

        $response = $client->get(
            "https://api.mercadopago.com/v1/payments/{$data['id_mercado_pago']}",
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$this->token}",
                ]
            ]
        );

        $responseBody = json_decode($response->getBody(), true);
        $statusNome = ($responseBody['status'] ?? '') === 'approved' ? 'Sucesso' : 'Pendente';

        $documento = null;

        if (!empty($data['cpf_remetente'])) {
            $documento = $data['cpf_remetente'];
        } elseif (!empty($data['cnpj_remetente'])) {
            $documento = $data['cnpj_remetente'];
        }

        TransferPix::create([
            'valor' => $data['valor'] ?? null,
            'nome_remetente' => $data['nome_remetente'] ?? null,
            'cpf_remetente' => $documento, // CPF ou CNPJ vai aqui
            'id_mercado_pago' => $data['id_mercado_pago'] ?? null,
            'id_pix' => $data['id_pix'] ?? null,
            'pos_id' => $responseBody['pos_id'] ?? null,
            'store_id' => $responseBody['store_id'] ?? null,
            'status' => $statusNome
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Recibo salvo com sucesso',
            'path' => $pdfPath
        ]);
    }

    public function getAllPix()
    {
        try {
            $beginDate = Carbon::now()->startOfDay();
            $endDate   = Carbon::now()->endOfDay();

            $transfers = TransferPix::get()->keyBy('id_mercado_pago');

            if ($transfers->isEmpty()) {
                return collect();
            }

            $pixReceipts = PixReceipt::select(
                'id',
                'valor',
                'id_payment',
                'status',
                'created_at'
            )
                ->whereBetween('created_at', [$beginDate, $endDate])
                ->whereIn('id_payment', $transfers->keys())
                ->get();

            return $pixReceipts->map(function ($pix) use ($transfers) {
                $transfer = $transfers->get($pix->id_payment);

                return [
                    'id'         => $pix->id,
                    'valor'      => $pix->valor,
                    'status'     => $pix->status,
                    'created_at' => $pix->created_at,
                    'id_payment' => $pix->id_payment,
                    'transfer_pix' => [
                        'nome_remetente'  => $transfer->nome_remetente,
                        'cpf_remetente'   => $transfer->cpf_remetente,
                        'id_mercado_pago' => $transfer->id_mercado_pago,
                        'id_pix'          => $transfer->id_pix,
                        'pos_id'          => $transfer->pos_id,
                        'store_id'        => $transfer->store_id,
                        'status'          => $transfer->status,
                    ]
                ];
            });
        } catch (\Exception $e) {
            return collect();
        }
    }


    public function getAllPixRefunded()
    {
        return PixReceipt::select(
            'id',
            'valor',
            'id_payment',
            'status'
        )->where('status', ['Estornado', 'Estornado - Módulo Offline'])->limit(10)->get();
    }

    public function getAllPixById($idUser)
    {
        try {
            $beginDate = Carbon::now()->startOfDay();
            $endDate   = Carbon::now()->endOfDay();

            // 1) IDs válidos em transfer_pix
            $validPayments = TransferPix::pluck('id_mercado_pago');

            // 2) Busca SOMENTE pix_receipts que existem em transfer_pix
            $pixReceipts = PixReceipt::select(
                'id',
                'valor',
                'id_payment',
                'status',
                'created_at'
            )
                ->where('id_user_internal', $idUser)
                ->whereBetween('created_at', [$beginDate, $endDate])
                ->whereIn('id_payment', $validPayments)
                ->get();

            if ($pixReceipts->isEmpty()) {
                return collect();
            }

            // 3) Busca transfer_pix correspondente
            $transfers = TransferPix::whereIn(
                'id_mercado_pago',
                $pixReceipts->pluck('id_payment')
            )
                ->get()
                ->keyBy('id_mercado_pago');

            // 4) Merge (agora 100% garantido que existe)
            $resultado = $pixReceipts->map(function ($pix) use ($transfers) {
                $transfer = $transfers->get($pix->id_payment);

                return [
                    'id'         => $pix->id,
                    'valor'      => $pix->valor,
                    'status'     => $pix->status,
                    'created_at' => $pix->created_at,
                    'id_payment' => $pix->id_payment,
                    'transfer_pix' => [
                        'nome_remetente'  => $transfer->nome_remetente,
                        'cpf_remetente'   => $transfer->cpf_remetente,
                        'id_mercado_pago' => $transfer->id_mercado_pago,
                        'id_pix'          => $transfer->id_pix,
                        'pos_id'          => $transfer->pos_id,
                        'store_id'        => $transfer->store_id,
                        'status'          => $transfer->status,
                    ]
                ];
            });

            return $resultado;
        } catch (\Exception $e) {
            return response()->json([
                'erro' => 'Erro ao buscar registros locais',
                'detalhe' => $e->getMessage()
            ], 500);
        }
    }


    public function getPaymentsTodayByID($idUser)
    {
        try {
            // Define o intervalo do dia atual
            $beginDate = Carbon::now()->startOfDay(); // 00:00:00 de hoje
            $endDate = Carbon::now()->endOfDay();     // 23:59:59 de hoje

            $data = PixReceipt::select('*')
                ->where('id_user_internal', $idUser)
                ->whereBetween('created_at', [$beginDate, $endDate])
                ->limit(10)
                ->get();

            return $data;
        } catch (\Exception $e) {
            $mensagem = 'Erro ao buscar registros locais: ' . $e->getMessage();
            return response()->json(['erro' => $mensagem], 500);
        }
    }

    public function getAllPixRefundedById($idUser)
    {
        return PixReceipt::select(
            'id',
            'valor',
            'id_payment',
            'status'
        )
            ->where('status', ['Estornado', 'Estornado - Módulo Offline'])
            ->where('id_user_internal', $idUser)
            ->limit(10)
            ->get();
    }

    public function getPagamentosHoje()
    {
        $client = new Client();
        $beginDate = Carbon::today()->toIso8601ZuluString();
        $endDate = Carbon::now()->toIso8601ZuluString();

        try {
            $response = $client->get('https://api.mercadopago.com/v1/payments/search', [
                'headers' => [
                    'Authorization' => "Bearer {$this->token}",
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'range' => 'date_created',
                    'begin_date' => $beginDate,
                    'end_date' => $endDate,
                ],
            ]);

            return $data = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $mensagem = $status === 403 ? 'Token inválido ou sem permissão.' : 'Erro inesperado ao consultar pagamentos.';
            return response()->json(['erro' => $mensagem], $status);
        }
    }

    public function getPagamentosHojeById()
    {
        $client = new Client();
        $beginDate = Carbon::today()->toIso8601ZuluString();
        $endDate = Carbon::now()->toIso8601ZuluString();

        try {
            $response = $client->get('https://api.mercadopago.com/v1/payments/search', [
                'headers' => [
                    'Authorization' => "Bearer {$this->token}",
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'range' => 'date_created',
                    'begin_date' => $beginDate,
                    'end_date' => $endDate,
                ],
            ]);

            return $data = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $mensagem = $status === 403 ? 'Token inválido ou sem permissão.' : 'Erro inesperado ao consultar pagamentos.';
            return response()->json(['erro' => $mensagem], $status);
        }
    }

    public function valueTotal($data)
    {
        return collect($data)
            ->filter(fn($item) => $item['status'] == 'Recebido')
            ->pluck('valor')
            ->whenEmpty(fn() => collect([0])) // Se estiver vazio, retorna 0
            ->pipe(function ($valores) {
                return $valores->count() > 1
                    ? $valores->sum()              // Soma tudo se tiver mais de 1
                    : $valores->first();           // Retorna único valor se só tiver 1
            });
    }

    public function valueTotalMaster($data)
    {
        return collect($data)
            ->filter(fn($item) => $item['status'] == 'approved')
            ->pluck('transaction_amount')
            ->whenEmpty(fn() => collect([0])) // Se estiver vazio, retorna 0
            ->pipe(function ($valores) {
                return $valores->count() > 1
                    ? $valores->sum()              // Soma tudo se tiver mais de 1
                    : $valores->first();           // Retorna único valor se só tiver 1
            });
    }

    public function executeChargeback($paymentId)
    {
        $client = new Client();

        $response = $client->post("https://api.mercadopago.com/v1/payments/{$paymentId}/refunds", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
                'X-Idempotency-Key' => Str::uuid()->toString(),
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);

        return $responseBody;
    }

    public function getPaymentById($paymentId)
    {
        $client = new Client();

        $response = $client->get("https://api.mercadopago.com/v1/payments/{$paymentId}", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json'
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);
        $dadosPagamento = [
            'external_reference'  => $responseBody['external_reference'] ?? null,
            'pos_id'              => $responseBody['pos_id'] ?? null,
            'status'              => $responseBody['status'] ?? null,
            'store_id'            => $responseBody['store_id'] ?? null,
            'transaction_amount'  => isset($responseBody['transaction_amount']) ? (int) floor($responseBody['transaction_amount'] / 1) : null,
            'id'                  => $responseBody['id'] ?? null,
            'transaction_id'      => $responseBody['transaction_details']['transaction_id'] ?? null,
        ];

        return $dadosPagamento;

        // Logando os valores
        Log::info("Dados extraídos do pagamento:", [
            'external_reference'   => $dadosPagamento['external_reference'],
            'pos_id'               => $dadosPagamento['pos_id'],
            'status'               => $dadosPagamento['status'],
            'store_id'             => $dadosPagamento['store_id'],
            'transaction_amount'   => $dadosPagamento['transaction_amount'],
            'id'                   => $dadosPagamento['id'],
            'transaction_id'       => $dadosPagamento['transaction_id'],
        ]);
    }

    public function getAllPayments()
    {
        $data = PixReceipt::get();

        return $data;
    }

    public function getPaymentsToday()
    {
        $data = PixReceipt::whereDate('created_at', Carbon::today())->get();

        return $data;
    }

    public function getPaymentsSevenDaysInternal()
    {
        // 1. Buscar registros dos últimos 7 dias
        $data = PixReceipt::where('created_at', '>=', Carbon::now()->subDays(7))->get();
        return $data;
    }

    public function getPaymentsLast30Days()
    {
        $data = PixReceipt::where('created_at', '>=', Carbon::now()->subDays(30))->get();
        return $data;
    }




    public function getAllPaymentsById($userId)
    {
        $data = PixReceipt::where('id_user_internal', $userId)->get();

        return $data;
    }

    public function getPaymentsSevenDaysInternalById($userId)
    {
        // 1. Buscar registros dos últimos 7 dias
        $data = PixReceipt::where('created_at', '>=', Carbon::now()->subDays(7))->where('id_user_internal', $userId)->get();;
        return $data;
    }

    public function getPaymentsLast30DaysById($userId)
    {
        $data = PixReceipt::where('created_at', '>=', Carbon::now()->subDays(30))->where('id_user_internal', $userId)->get();;
        return $data;
    }
























    public function getUsers()
    {
        return User::get();
    }

    public function getUsersById($idUser)
    {
        return User::where('id', $idUser)->first();
    }


    public function getLatestRefunds()
    {
        $client = new Client();

        try {
            $response = $client->get('https://api.mercadopago.com/v1/payments/search', [
                'headers' => [
                    'Authorization' => "Bearer {$this->token}",
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'sort' => 'date_created',
                    'criteria' => 'desc',
                    'limit' => 10,
                    'status'   => 'refunded',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $mensagem = $status === 403
                ? 'Token inválido ou sem permissão.'
                : 'Erro inesperado ao consultar pagamentos.';
            return response()->json(['erro' => $mensagem], $status);
        }
    }

    public function getPaymentInternalById($idPayment)
    {
        $data = PixReceipt::where('id', $idPayment)->get();
        $data[0]->modulo = Module::where('id', $data[0]->external_reference)->value('modulo');

        $storeIds = $data->pluck('store_id')->unique();
        $posIds = $data->pluck('pos_id')->unique();
        $ids = $data->pluck('id_payment'); // reduzido ao escopo dos últimos 7 dias

        // 3. Buscar os receipts correspondentes
        $dataReceipt = TransferPix::whereIn('id_mercado_pago', $ids)->get()->keyBy('id_mercado_pago');

        // 4. Obter nomes das lojas e POS
        $client = new Client();
        $storeNames = [];
        $posNames = [];

        foreach ($storeIds as $storeId) {
            try {
                $response = $client->get("https://api.mercadopago.com/stores/{$storeId}", [
                    'headers' => [
                        'Authorization' => "Bearer {$this->token}",
                        'Content-Type' => 'application/json',
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                $storeNames[$storeId] = $body['name'] ?? 'Loja sem nome';
            } catch (\Exception $e) {
                $storeNames[$storeId] = 'Erro ao buscar nome da loja';
            }
        }

        foreach ($posIds as $posId) {
            try {
                $response = $client->get("https://api.mercadopago.com/pos/{$posId}", [
                    'headers' => [
                        'Authorization' => "Bearer {$this->token}",
                        'Content-Type' => 'application/json',
                    ]
                ]);
                $body = json_decode($response->getBody(), true);
                $posNames[$posId] = $body['name'] ?? 'POS sem nome';
            } catch (\Exception $e) {
                $posNames[$posId] = 'Erro ao buscar nome do POS';
            }
        }

        // 5. Juntar dados no resultado final
        $result = $data->map(function ($payment) use ($storeNames, $posNames, $dataReceipt) {
            $payment->store_name = $storeNames[$payment->store_id] ?? 'Desconhecida';
            $payment->pos_name = $posNames[$payment->pos_id] ?? 'Desconhecido';
            $payment->receipt = $dataReceipt[$payment->id_payment] ?? null;

            return $payment;
        });

        return $result;
    }

    public function reversalAction($idPayment): bool
    {
        try {
            DB::transaction(function () use ($idPayment) {
                // Atualiza status da transferência PIX
                $updatedTransfer = PixReceipt::where('id_payment', $idPayment)->update([
                    'status' => 'refunded',
                ]);

                // Atualiza status do recibo PIX
                $updatedReceipt = PixReceipt::where('id_mercado_pago', $idPayment)->update([
                    'status' => ' Estornado',
                ]);

                // Verifica se houve update em ambas as tabelas
                if ($updatedTransfer === 0 || $updatedReceipt === 0) {
                    throw new \Exception("Nenhum registro encontrado para o pagamento {$idPayment}");
                }

                // Executa procedimento de chargeback
                $this->executeChargeback($idPayment);
            });

            return true;
        } catch (\Throwable $e) {
            // Loga erro para análise
            Log::error("Falha ao reverter pagamento PIX {$idPayment}: " . $e->getMessage());

            return false;
        }
    }

    public function getModuloByMercadoPagoId($pos_id)
    {
        return Store::where('idStoreMercadoPago', $pos_id)
            ->value('modulo');
    }
}
