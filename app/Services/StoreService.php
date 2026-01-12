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

        $response = $client->get("https://www.mercadopago.com.br/money-out/transfer/api/receipt/pix_pdf/{$paymentId}/pix_account/pix_payment.pdf", [
            'headers' => [
                'cookie' => 'ftid=lfgV8PvFoGSSWOpBvcFMRn24tFTxXK1c-1748716757421; cookiesPreferencesNotLogged=%7B%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; _ga=GA1.1.1151310564.1750027010; _d2id=685dd283-c062-4275-aa01-8effbc1829d7; p_dsid=da33d370-eecc-4d0c-bdad-d4ba939f5336-1750033492551; _hjSessionUser_492923=eyJpZCI6ImY4MmE3MmIwLWYwNzYtNWIwYy05MWQ0LWVjZGQ4NTliMjI3OCIsImNyZWF0ZWQiOjE3NTAwMzM1NzM5ODIsImV4aXN0aW5nIjp0cnVlfQ==; dxDevPanelOnboarding=true; mp_dx-dev-panel-production-product=true; dsid=0fefc014-9782-40cb-899c-87b1231598a9-1750037809126; _hjSessionUser_4992954=eyJpZCI6IjFjNDQ0NTZiLTBhMjYtNWEzMi1iZWY2LTlmZmE5ZTdlMmJkMiIsImNyZWF0ZWQiOjE3NTAwMzM5MjEzNjgsImV4aXN0aW5nIjp0cnVlfQ==; edsid=6d74e3a5-7cf6-36be-998f-1383cebf5ce5-1750041657073; _gcl_au=1.1.2014684061.1751830363; _tt_enable_cookie=1; _ttp=01JZGKKZ7YCQWRDASWKDH1J19Z_.tt.2; ttcsid=1751830363395::W9hYcpxOhqo_8M8AB8f8.1.1751830363397; ttcsid_CU409KRC77U5T1OVP8KG=1751830363395::StSXEKCYzznSrXXdndHW.1.1751830364394; ttcsid_CUV1O83C77UCOV2E0PVG=1751830363395::6uLXzsd9GcsdY9GHGX8i.1.1751830364394; ttcsid_CUN021JC77U74NKAGB7G=1751830363396::3Bsvh38eBmMzgcTyeyFA.1.1751830364394; ttcsid_CUJ1CJ3C77U09FJDRBMG=1751830363396::SgtGHiWNL28jMlUO8lIV.1.1751830364394; ttcsid_CVDCTDJC77UCRE2PIO20=1751830363396::dUIjZALuDsxEEY-U5aJp.1.1751830364395; ttcsid_D04F33RC77U7UBOAPF1G=1751830363397::cMkN1xl2HklUe-Kpw8H7.1.1751830364395; _csrf=_w5oqo20sO3wcEr6_rEiLEuq; orgnickp=DHGCBDEAF12717; orguserid=9Zh009d9ZHh09; ssid=ghy-092812-l86VDIgQm8Wpbdpkz05jIV0klpYRGT-__-2710523715-__-1853772058106--RRR_0-RRR_0; mp_wsid=eyJhbGciOiJSUzI1NiIsImtpZCI6IlBNREhlSGt2WEdPZ2JmWFNXZ2VnMDRzeEVTZG1yV0N1TndKcFl1N3lGUjg9IiwidHlwIjoiSldUIn0.eyJhZG1pbl9vdHAiOmZhbHNlLCJhdXRoX2Zsb3ciOiJhY2Nlc3MiLCJjbGllbnRfaWQiOjY0OTUyMTMwOTkwNDY1NywiZXhwIjoxODUzNzcyMDU4LCJpYXQiOjE3NTkwNzc2NTgsImlzcyI6InVybjphdXRoLXNlcnZlcjpzZXNzaW9ucyIsImp0aSI6IjNmMzI5ZGEyLWFiNzAtNGRkMi1hN2JhLThiNTUyNzBjODdmNiIsInByb2R1Y3RfaWQiOjIsInNpdGVfaWQiOiJtbGIiLCJzc2kiOiJlOGU2NjdjNy00OGE0LTQzOTAtYjk1Ni0xZmJhOTUzZmVhZjAiLCJzdWIiOiJ1cm46dXNlcnM6MjcxMDUyMzcxNSJ9.lG7Vn53Kji6m82m5cI_w18UHbiDKP4K_doTD2ynQf8rpo_y8r0-IsU32BT41-xGB6KQ5r8mXdhfDPp0bcPmQClp9xxjNf_fdJgTGlddTDPQbzHme27auT7t-UhpwcUhpgyjClGYpkhN3WvAUQqf0nnEdAKd66wonswrJ_JvQ2AT-w_JJwCF_Oqn0kOg9YKbuJPpydTItH9kujdELPuZeKhZZfyay_mMK2knpkWllMUtNF_d0ex9U1UmYC3bbXPxfLv4Co0CSR2v9C1OJ1PUswxaijkxz1OzxqZjcRbI2DYlQilGu3Cdem6VS-f82e-qj_bAzn8PSf3fxRRJOemwrtw; orguseridp=2710523715; cookiesPreferencesLoggedFallback=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; dxDevPanelAppCollaboratorsModal=true; QSI_HistorySession=https%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1759097952080; QSI_SI_23SnzFDvtxRGSZ8_intercept=true; _mldataSessionId=f0edd1dc-8323-4e82-be2b-1531b212e7d8; QSI_SI_dgmzu5WdT6dFzHU_intercept=true; ttl=1759270926925; NSESSIONID_pampa_session=s%3AXlcK225hYo_3FvnJf5If-nGT73SU9fae.FbFQKMMlWC6qNfFVx9EXCubDamOObeA4lS%2F8KGi6faw; rtid=13a338dd-a945-4c94-99a7-10808950b660; NSESSIONID_qrtsid=s%3AwlteWzq7ap8UCVvv6ZxY5MbpbSz7TWoc.VyLjurdyT%2FTA6DEZFD%2FlxTBREhBT4r5qPQ9NqB4J7nM; _ga_XJRJL56B0Z=GS2.1.s1759270628$o35$g1$t1759271400$j60$l0$h0; p_edsid=18d53813-9f06-355b-b0e9-6fbc6188a145-1759271377552; QSI_SI_3I7KQXuLnzde3sy_intercept=true; x-meli-session-id=armor.998731cde0ee5070a4ca2c877d8037625696ddc757d4dfdeb036337996dd4a8e8ba3e8ed2b0a2857d813fabd2cf9202663b2e571190688b23fb5d17ed14813a985fa37ec751814404ad73f619f106e526efb2030b41c0791112c1d10828d4140.8f0a5e16679963313696ee4b18cb4967; nsa_rotok=eyJhbGciOiJSUzI1NiIsImtpZCI6IjMiLCJ0eXAiOiJKV1QifQ.eyJpZGVudGlmaWVyIjoiZThlNjY3YzctNDhhNC00MzkwLWI5NTYtMWZiYTk1M2ZlYWYwIiwicm90YXRpb25faWQiOiIwOTU1NjJlOS1lYjNlLTQ4ZDEtYWQ1My1hZjk1NWRjZmNkZTkiLCJwbGF0Zm9ybSI6Ik1QIiwicm90YXRpb25fZGF0ZSI6MTc1OTI3MjQ4NiwiZXhwIjoxNzYxODYzODg2LCJqdGkiOiIyYWU3MzkxNy1jYzVjLTRjYzQtYThmZC1kMWU2OWNiNzA4ODYiLCJpYXQiOjE3NTkyNzE4ODYsInN1YiI6ImU4ZTY2N2M3LTQ4YTQtNDM5MC1iOTU2LTFmYmE5NTNmZWFmMCJ9.ClzdmaTdxgeutxg9MwYRzXfJN7vwjjXUg8DDNnY5KH5UKaD4EzyjsjdV_XUinUfgS0pr4b8drbPG_K3quH_pDwS_qHpGSEl_wWDPQyLbuW1owgYzswb7Qn78Aqogjl9x9BkbZjKSDO5-e8d7LwyWeTQsvrqNo_vrobjOgU5crBIsq_2Meh-odGTOIO3xNItrJ4_jYXbUZxwo-I9Ek-rp2Pc8BHPyIlNfMaPyrH461jOLjd_hb_hGBvQxTEZvogvH6giFkjwvj5rFCynUHhgJsnqCxQsBag7ehR2asASpA7i9MKJGaV8YQBIngUx211cPQpTqnHfelU71EyYemb3Cvw',
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1',
                'Accept' => 'application/pdf',
                'Authorization' => "Bearer {$this->token}",
                'referer' => 'https://www.mercadopago.com.br/activities/detail/qr_merchant_order-2593e625f9f575974e8524a4b86b51d372d18db6',
                'cache-control' => 'max-age=0',
                'accept-language' => 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
                'Content-Type' => 'application/json'
            ],
            'stream' => false // caso queira fazer download do PDF direto
        ]);
        $content = $response->getBody()->getContents();

        // 🔽 Loga os primeiros 500 caracteres da resposta para análise
        Log::debug("Conteúdo retornado para o PDF: " . substr($content, 0, 500));
        $directory = storage_path("logs/recibos");
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        $pdfPath = $directory . "/recibo_{$paymentId}.pdf";
        file_put_contents($pdfPath, $response->getBody());

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);

        // Texto completo
        $text = $pdf->getText();


        $lines = explode("\n", $text);
        $data = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);

            if (Str::contains($line, 'às') && Str::contains($line, 'de março')) {
                preg_match('/(\d{2}) de (\w+) de (\d{4}), às (\d{2}:\d{2}:\d{2})/', $line, $matches);
                if ($matches) {
                    $meses = ['janeiro' => '01', 'fevereiro' => '02', 'março' => '03', 'abril' => '04', 'maio' => '05', 'junho' => '06', 'julho' => '07', 'agosto' => '08', 'setembro' => '09', 'outubro' => '10', 'novembro' => '11', 'dezembro' => '12'];
                    $mes = $meses[strtolower($matches[2])] ?? '00';
                    $data['data_transferencia'] = "{$matches[1]}/{$mes}/{$matches[3]} {$matches[4]}";
                }
            }

            if (Str::startsWith($line, 'R$')) {
                $valorLimpo = preg_replace('/[^\d,]/', '', $line);
                $valor = str_replace(',', '.', $valorLimpo);
                $data['valor'] = floatval($valor);
            }

            if ($line === 'De') {
                $data['nome_remetente'] = $lines[$index + 1] ?? '';
                $data['cpf_remetente'] = trim(str_replace('CPF:', '', $lines[$index + 2] ?? ''));
            }

            if (Str::startsWith($line, 'Número da transação do Mercado Pago')) {
                $data['id_mercado_pago'] = $lines[$index + 1] ?? '';
            }

            if (Str::startsWith($line, 'ID de transação PIX')) {
                $data['id_pix'] = $lines[$index + 1] ?? '';
            }
        }






        $response = $client->get("https://api.mercadopago.com/v1/payments/{$data['id_mercado_pago']}", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->token}",
            ]
        ]);
        $responseBody = json_decode($response->getBody(), true);
        $statusNome = $responseBody['status'] === 'approved' ? 'Sucesso' : 'Pendente';

        TransferPix::create([
            'valor' => $data['valor'],
            'nome_remetente' => $data['nome_remetente'],
            'cpf_remetente' => $data['cpf_remetente'],
            'id_mercado_pago' => $data['id_mercado_pago'],
            'id_pix' => $data['id_pix'],
            'pos_id' => $responseBody['pos_id'],
            'store_id' => $responseBody['store_id'],
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

        $storeIds = $data->pluck('store_id')->unique();
        $posIds = $data->pluck('pos_id')->unique();
        $ids = $data->pluck('id_payment'); // reduzido ao escopo dos últimos 7 dias

        // 3. Buscar os receipts correspondentes
        $dataReceipt = PixReceipt::whereIn('id_mercado_pago', $ids)->get()->keyBy('id_mercado_pago');

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
}
