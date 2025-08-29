<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\User;
use App\Models\PixReceipt;
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
        $this->baseUrl = "https://api.mercadopago.com/users/275391771/stores/search";
        $this->token = "APP_USR-3681603214139934-082109-4332a58fb3448f558a674192d4705b10-275391771";
        $this->idUser = "275391771";
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
                'external_id' => "mccf{$moduloValue}",
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

        $moduloValueFormated = str_replace('-', '', $moduloValue);

        $response = $client->put("https://api.mercadopago.com/instore/qr/seller/collectors/{$this->idUser}/stores/{$idStore}/pos/{$moduloValueFormated}/orders", [
            'headers' => [
                'Authorization' => "Bearer {$this->token}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'title' => "Pedido do Cliente",
                'description' => "Produto ou serviço escolhido pelo cliente",
                'notification_url' => "https://3756e3dd107a.ngrok-free.app/notifications",
                'external_reference' => "$moduloValue",
                "expiration_date" => "2999-12-30T01:30:00.000-03:00",
                'total_amount' => 0,
                'items' => [ // <- agora um array de objetos
                    [
                        'id' => "item1",
                        'title' => "Produto X",
                        'unit_measure' => "unit",
                        'unit_price' => 0.00,
                        'quantity' => 1,
                        'total_amount' => 0
                    ]
                ]
            ],
        ]);
        //Log::info("posData1: ". $response);
        $responseBody = json_decode($response->getBody(), true);
        Log::info("posData1: https://api.mercadopago.com/instore/qr/seller/collectors/{$this->idUser}/stores/{$idStore}/pos/{$moduloValueFormated}/orders");
        Log::info("data1: " . $moduloValue);
        return $responseBody;
    }


    public function getPixReceiptPdf($paymentId)
    {
        $client = new Client();

        $response = $client->get("https://www.mercadopago.com.br/money-out/transfer/api/receipt/pix_pdf/{$paymentId}/pix_account/pix_payment.pdf", [
            'headers' => [
                'cookie' => 'd2id=acfa5858-1286-45d6-b756-75fe9b7dec5b; ftid=CutcDX9u9eUx0UOmXcbOroxwWYroBOB4-1741203837782; _gcl_au=1.1.987169972.1755697440; _hjSessionUser_1798523=eyJpZCI6ImU0NjQ4Nzg4LTRkNDAtNTlhNS1hNTE3LTNlOGM4MmJhYjRhZCIsImNyZWF0ZWQiOjE3NTU2OTc0NDY0NjgsImV4aXN0aW5nIjp0cnVlfQ==; _fbp=fb.2.1755697446643.452899114492923725; _csrf=7L-3fspxedA3f-87_umKKGfT; QSI_SI_23SnzFDvtxRGSZ8_intercept=true; p_dsid=685e0644-bafc-423c-b9e1-8b96b68f3dd9-1755697758431; c_landingdesktop=3.0.0; _hjSessionUser_492923=eyJpZCI6IjEwNjgzZmQwLWMyMGYtNTUyMS05YTI3LWNkMmIxZmY3ZTRjMyIsImNyZWF0ZWQiOjE3NTU2OTc5NjExNDUsImV4aXN0aW5nIjp0cnVlfQ==; _tt_enable_cookie=1; _ttp=01K33W1PJT602KTJ681F3TPPK4.tt.2; ttcsid_CU409KRC77U5T1OVP8KG=1755697961571::p7Xr52Pc3O-qgboM92z7.1.1755697961810; orgnickp=DCFHDBGAE66835; orguseridp=275391771; cookiesPreferencesLoggedFallback=%7B%22userId%22%3A275391771%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; cookiesPreferencesNotLogged=%7B%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; QSI_HistorySession=https%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1755697700273%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1755697724540%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1755698061196; _hs_cookie_cat_pref=1:true_2:true_3:true; hubspotutk=4f9920501358db0cbc40a484580ec250; __hssrc=1; _hjSessionUser_4992954=eyJpZCI6IjUyMGRlMDdhLTcxYzMtNWI3NC05ZTZkLTdhOWY0MDJjMTIxMyIsImNyZWF0ZWQiOjE3NTU3ODM5MjkyNDUsImV4aXN0aW5nIjpmYWxzZX0=; _ml_ar-browser-check=36f9b8ab-62fe-4efe-acc4-b4744fab5bbc; dxDevPanelOnboarding=true; mp_dx-dev-panel-production-product=true; dx-test-users-onboarding-=true; c_balance-frontenddesktop=3.57.1; _ga_XJRJL56B0Z=GS2.1.s1755789291$o2$g0$t1755789291$j60$l0$h0; _ga=GA1.3.213525301.1755698503; _gid=GA1.3.1177246012.1755800686; _hjSession_492923=eyJpZCI6IjM4ZTFjNWJkLTU3YTUtNDc5ZS1iMjI4LWU0NmNhYzc5NWRlYiIsImMiOjE3NTU4MDA2ODY3NDMsInMiOjEsInIiOjAsInNiIjowLCJzciI6MCwic2UiOjAsImZzIjowLCJzcCI6MH0=; ttcsid=1755800686912::k96RP_wFt07U0M6WNFCz.2.1755800687331; cto_bundle=Nstj6V96ak9MN2FmUklpZjB5dDh1dmF4dXRGYWNzUzNEZThPc0ZWd1RKczhIdWEzTWJMUXJBbHBVM3lIM2slMkZtMjVJNld3a1BEbHZsbWJKRUN3RnFmbnRzYTMxdXk2RTF1VDRDbTlhWEslMkI4SnZwR0swYVVoWVRFa09rNHdlQzYzb0luJTJGNDJxd3hOc0pPQnNDWVZJMllVcWNmVXI5aWRBWkdobWR5MTdFM3dYODFFWHR4OXlLRnpCV04zQURjd00lMkI5TWU3JTJCOWRXdzBMT1YlMkZFcjRwJTJCbUtMSHl6d210UUp6aXU2YVdNZjZEJTJGOFlTRExoVkVmbE5KNk1sRThwM2VyWVk3ZTROODZHdkVUbXY3ZCUyRjFvcjR2U3VjeWNTQSUzRCUzRA; ttcsid_CUN021JC77U74NKAGB7G=1755800686911::wDiLMpr4fGHNcuTMd-ML.2.1755800688493; ttcsid_CUJ1CJ3C77U09FJDRBMG=1755800686913::JYqWtTtRUwT5C-4wxuh0.2.1755800688493; ttcsid_CVDCTDJC77UCRE2PIO20=1755800686914::pZVBRpoSNIBGLUvVBS_U.2.1755800688493; ttcsid_D04F33RC77U7UBOAPF1G=1755800686985::eq574vUOItR46OKwyR2A.2.1755800688493; ttcsid_CUV1O83C77UCOV2E0PVG=1755800687330::ZQgYeNidkV5e3pyeXcJU.2.1755800688494; __hstc=262728915.4f9920501358db0cbc40a484580ec250.1755697977224.1755697977224.1755800689782.2; __hssc=262728915.1.1755800689782; app-theme=yellowblue-light; _mldataSessionId=c4b33ecb-9f41-4ece-b304-a3d91f75957e; cookiesPreferencesLogged=%7B%22userId%22%3A275391771%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; orguserid=ZZh79hH40hh0; ssid=ghy-082114-rMc4HNYXlHAbnV78DIRpaRWLbg6mI8--275391771--1850495293329--RRR_0-RRR_0; mp_wsid=eyJhbGciOiJSUzI1NiIsImtpZCI6IlBNREhlSGt2WEdPZ2JmWFNXZ2VnMDRzeEVTZG1yV0N1TndKcFl1N3lGUjg9IiwidHlwIjoiSldUIn0.eyJhZG1pbl9vdHAiOmZhbHNlLCJhdXRoX2Zsb3ciOiJhY2Nlc3MiLCJjbGllbnRfaWQiOjY0OTUyMTMwOTkwNDY1NywiZXhwIjoxODUwNDk1MjkzLCJpYXQiOjE3NTU4MDA4OTMsImlzcyI6InVybjphdXRoLXNlcnZlcjpzZXNzaW9ucyIsImp0aSI6IjA0MWMyMDlmLTdmYjQtNDA2NS1iZjhjLWE5NTljNTFmZjg0ZiIsInByb2R1Y3RfaWQiOjIsInNpdGVfaWQiOiJtbGIiLCJzc2kiOiIzNmEyMDk0OC1mYzkxLTQ3NmUtYWJkZC0yYWIzN2JmYjUzNTIiLCJzdWIiOiJ1cm46dXNlcnM6Mjc1MzkxNzcxIn0.XuN92SoAaLlpPT-IjnLG4QFrNfMdjVWhlgccsgzvZ7KuoKEKCejmZC_cU7nMsUNrn7Wn8GdqJ5_C3n0HbiRcJsm4idsRd083hsjnu_EUrnJmY-bVg5aEkJtDyzTPWRMrGcNlsiAtDTMoqH63okLGij2rLLFC8At6Ax3jbz3p5SuLQoFVLUh8dXf1oFvGXemDnrJKXBN6_fRgVnQ3fLlSoXyDhRPTBRsYBYMoDz9nIFMEAqdm7AxWvlz9bVIHWyEe20krJkFUuV9WKHYegEF5AhX7M-5goyj-sgMrpHPGwQQXfzxAd0AoR6Ah3mHivACxppwcn6UEJ9RuPO3Yk891KA; hide-cookie-banner=275391771-COOKIE_PREFERENCES_ALREADY_SET; ttl=1755800895263; p_edsid=f2c2c325-b604-3b56-afea-979eb0743fe4-1755800896728; x-meli-session-id=armor.5fe6d89ed86fae62caca611a948a75b49dfb10fca4985d4ce614b5aa871b1f234ae7a028cb2006f9505cc4796bb67e8fbaef66c02a7b98435a3b6eba23e2cc2686f7ca8f99d39f43dadfc8b5565182fc81cd5a8895cd83ad8de19641b11e668d.af8aa9957a2974a3c28dd061b9d74029; nsa_rotok=eyJhbGciOiJSUzI1NiIsImtpZCI6IjIiLCJ0eXAiOiJKV1QifQ.eyJpZGVudGlmaWVyIjoiMzZhMjA5NDgtZmM5MS00NzZlLWFiZGQtMmFiMzdiZmI1MzUyIiwicm90YXRpb25faWQiOiJhZWY5YjJjZS02MDk4LTRlYWUtODczMy1jMzFkY2NjN2RmNTUiLCJwbGF0Zm9ybSI6Ik1QIiwicm90YXRpb25fZGF0ZSI6MTc1NTgwMTQ5NCwiZXhwIjoxNzU4MzkyODk0LCJqdGkiOiIwMzZiYWM5Ny1mOTVlLTQ1ZDktYWYyNS0zZmFmYjBjMDBhZDMiLCJpYXQiOjE3NTU4MDA4OTQsInN1YiI6IjM2YTIwOTQ4LWZjOTEtNDc2ZS1hYmRkLTJhYjM3YmZiNTM1MiJ9.F0r5CdBZSt3N4u1CMYpXTUXgqJRItAGeM716fUgw9enQDIjrtIoW1ja_XZL0LARZzSqjw7bdiLSA6jELPWIMx5AlKMKXUNpG43ywC3SS1vSsVIBpeGyl5TuYokw-of6m95byucUO3z7z5oaKSouodfEoHvgEfxpgOjMnxLGKZdrCxCIdHl9V8xM54E8pZHlud2MBka6QofpokgbcbicpxCGvaXOlt0PUW_cZ55HODIgR_3Osjf8ssIaTGLpdc4RyBzv4zy7YtHrJMLXkezhvpL0iIYJhONC8Xft8TKm33OflvSXF_NYXM97CYsAZRVfN_sN3381_5NIQh-Xcv5-JA',
                'User-Agent' => 'Mozilla/5.0',
                'Accept' => 'application/pdf',
                'Authorization' => "Bearer {$this->token}",
            ],
            //'stream' => false // caso queira fazer download do PDF direto
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




        PixReceipt::create([
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
        return PixReceipt::select(
            'id',
            'valor',
            'nome_remetente',
            'cpf_remetente',
            'id_mercado_pago'
        )->where('status', 'Sucesso')->limit(10)->get();
    }

    public function getAllPixRefunded()
    {
        return PixReceipt::select(
            'id',
            'valor',
            'nome_remetente',
            'cpf_remetente',
            'id_mercado_pago'
        )->where('status', ' Estornado')->limit(10)->get();
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

    public function valueTotal($data)
    {
        return collect($data['results'])
            ->filter(fn($item) => $item['status'] === 'approved')
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
        $data = TransferPix::get();

        // 2. Coletar store_ids e pos_ids únicos
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

    public function getPaymentsToday()
    {
        $data = TransferPix::whereDate('created_at', Carbon::today())->get();

        // 2. Coletar store_ids e pos_ids únicos
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

    public function getPaymentsSevenDaysInternal()
    {
        // 1. Buscar registros dos últimos 7 dias
        $data = TransferPix::where('created_at', '>=', Carbon::now()->subDays(7))->get();

        // 2. Coletar store_ids e pos_ids únicos
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

    public function getPaymentsLast30Days()
    {
        $data = TransferPix::where('created_at', '>=', Carbon::now()->subDays(30))->get();

        // 2. Coletar store_ids e pos_ids únicos
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

    public function getUsers()
    {
        return User::get();
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
        $data = TransferPix::where('id', $idPayment)->get();

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
                $updatedTransfer = TransferPix::where('id_payment', $idPayment)->update([
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
