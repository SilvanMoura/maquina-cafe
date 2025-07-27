<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Store;
use App\Models\PixReceipt;
use App\Models\TransferPix;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
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
        $this->baseUrl = "https://api.mercadopago.com/users/2321161890/stores/search";
        $this->token = "APP_USR-7226859123041588-031023-d12365cba1d9c1e218e36c78ae493db2-2321161890";
        $this->idUser = "2321161890";
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
                'notification_url' => "https://74d3-2804-14d-403a-8011-4019-f63b-2c27-f3fc.ngrok-free.app/notifications",
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
                'cookie' => '_csrf=vhotNcZdK4ZcIJ5Ol_W3qwwj; orguseridp=2321161890; ftid=lfgV8PvFoGSSWOpBvcFMRn24tFTxXK1c-1748716757421; orgnickp=MM20250310220410; cookiesPreferencesNotLogged=%7B%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; _ga=GA1.1.1151310564.1750027010; _d2id=685dd283-c062-4275-aa01-8effbc1829d7; p_dsid=da33d370-eecc-4d0c-bdad-d4ba939f5336-1750033492551; _hjSessionUser_492923=eyJpZCI6ImY4MmE3MmIwLWYwNzYtNWIwYy05MWQ0LWVjZGQ4NTliMjI3OCIsImNyZWF0ZWQiOjE3NTAwMzM1NzM5ODIsImV4aXN0aW5nIjp0cnVlfQ==; dxDevPanelOnboarding=true; mp_dx-dev-panel-production-product=true; dsid=0fefc014-9782-40cb-899c-87b1231598a9-1750037809126; _hjSessionUser_4992954=eyJpZCI6IjFjNDQ0NTZiLTBhMjYtNWEzMi1iZWY2LTlmZmE5ZTdlMmJkMiIsImNyZWF0ZWQiOjE3NTAwMzM5MjEzNjgsImV4aXN0aW5nIjp0cnVlfQ==; edsid=6d74e3a5-7cf6-36be-998f-1383cebf5ce5-1750041657073; c_landing__desktop=2.92.0; _gcl_au=1.1.2014684061.1751830363; _tt_enable_cookie=1; _ttp=01JZGKKZ7YCQWRDASWKDH1J19Z_.tt.2; ttcsid=1751830363395::W9hYcpxOhqo_8M8AB8f8.1.1751830363397; ttcsid_CU409KRC77U5T1OVP8KG=1751830363395::StSXEKCYzznSrXXdndHW.1.1751830364394; ttcsid_CUV1O83C77UCOV2E0PVG=1751830363395::6uLXzsd9GcsdY9GHGX8i.1.1751830364394; ttcsid_CUN021JC77U74NKAGB7G=1751830363396::3Bsvh38eBmMzgcTyeyFA.1.1751830364394; ttcsid_CUJ1CJ3C77U09FJDRBMG=1751830363396::SgtGHiWNL28jMlUO8lIV.1.1751830364394; ttcsid_CVDCTDJC77UCRE2PIO20=1751830363396::dUIjZALuDsxEEY-U5aJp.1.1751830364395; ttcsid_D04F33RC77U7UBOAPF1G=1751830363397::cMkN1xl2HklUe-Kpw8H7.1.1751830364395; orguserid=HZHZZT00t074d; ssid=ghy-070723-e1B84KPtxF4TzWk6sxXKnHbzRh6zL7-__-2321161890-__-1846638592437--RRR_0-RRR_0; mp_wsid=eyJhbGciOiJSUzI1NiIsImtpZCI6IlBNREhlSGt2WEdPZ2JmWFNXZ2VnMDRzeEVTZG1yV0N1TndKcFl1N3lGUjg9IiwidHlwIjoiSldUIn0.eyJhZG1pbl9vdHAiOmZhbHNlLCJhdXRoX2Zsb3ciOiJhY2Nlc3MiLCJjbGllbnRfaWQiOjY0OTUyMTMwOTkwNDY1NywiZXhwIjoxODQ2NjM4NTkyLCJpYXQiOjE3NTE5NDQxOTIsImlzcyI6InVybjphdXRoLXNlcnZlcjpzZXNzaW9ucyIsImp0aSI6ImU4NTdmMDcwLTU5MmQtNDUwNi05ODk5LTI0NTMzNjcyMjE0NCIsInByb2R1Y3RfaWQiOjIsInNpdGVfaWQiOiJtbGIiLCJzc2kiOiIyM2EwMTgzOS04NjM3LTQ1OTUtOTYxZi0yNDRlOWEyZTZhNmMiLCJzdWIiOiJ1cm46dXNlcnM6MjMyMTE2MTg5MCJ9.069Tcx46nAYKcjyb28K8BcUbj-blvidP_XzEIng1i6CpRY92YRCZ_vWfzV7xr9C8R8DQ4NUvn22r03o6dGAON8ta6-ZIaKlh2BO3bs8WAUs2Z3EzIQQNZUM3gk9-iTDpdGmMqsW5Cj-58VnYAbJ9znSArUjA6kf9lr2xeNepC2PfQdwBxgUy05N-5Az0LJz66sZDmyktaXyfUW1_lkkqOGXVmRICrqWeJebOUFjXkK7GU7BGCVdtUvk-Fp4_F9hidF5embDs4Ttn9sJ0F_RVSjDR6R2khSgyHQSP11hFVLpzfal-Zk3e-N6GBtdmAtTUJr5et4cfIVFmMn-oVHp81Q; cookiesPreferencesLoggedFallback=%7B%22userId%22%3A2321161890%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; QSI_HistorySession=https%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1750033485788%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1750038373100%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1750041587055%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1750042466948%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1750369799086%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1750383009884%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1751944184749; _ga_XJRJL56B0Z=GS2.1.s1753236410$o9$g1$t1753237317$j60$l0$h0; c_balance-frontend__desktop=3.54.3; nsa_rotok=eyJhbGciOiJSUzI1NiIsImtpZCI6IjIiLCJ0eXAiOiJKV1QifQ.eyJpZGVudGlmaWVyIjoiMjNhMDE4MzktODYzNy00NTk1LTk2MWYtMjQ0ZTlhMmU2YTZjIiwicm90YXRpb25faWQiOiJmY2M4NWFjMi1iZjY0LTQ1NWMtOGFmYi1hYjk0Y2FhMzdiZDkiLCJwbGF0Zm9ybSI6Ik1QIiwicm90YXRpb25fZGF0ZSI6MTc1MzMyNzc1NiwiZXhwIjoxNzU1OTE5MTU2LCJqdGkiOiI1MzkxNjJlOC0xN2E5LTQ0MmQtYWFkYS00ODgyYTFlMDEzOTQiLCJpYXQiOjE3NTMzMjcxNTYsInN1YiI6IjIzYTAxODM5LTg2MzctNDU5NS05NjFmLTI0NGU5YTJlNmE2YyJ9.hrQRfm16B-zEHvbpoTvkvAvriqaFRZXXEAq9yhEgoVgnOgX197ps8y4aktcfqJMTqsZJA3F6qdQ-K1zJDgBeWZiio1gfbBAW0WlFWyAfnjVEe4aZG0J1ACQ10s8H7V8RCoUOPmLHITRJhbzngjlM0Q8plNTokdz_5BaZnnIfMbG3aomzcDe0FTtling9DrCLAqRiA_bZ2cC3k3dnOL6_lHqDa40eHI131BAY84oAyZ6_gI7i7wR-lb49yuf7oTztB0ymNc1vjoJSnzmJ1ynByiKnj87pCeyNG1boifYE4gkY41wFkQcdlhT45dW9gH6X6sdgZ6jITXge1z-Xw7smnw; p_edsid=eb00ba21-001d-3fac-be8f-3ec51558138b-1753327159705; ttl=1753327192057; x-meli-session-id=armor.423535f7ff08890faeab4da8e03b31ae9255d4cebde93ce5dfff5f3bcb99f79deff3634f492ae8f95c3a0ceb583e6fffa82f4a3865010698597aa1461b7c347344f3f5146f72ac881935f2c26a1e9156b9e86c49e11bf362ba81990425697065.9eabc85e3a34dd597aa16946e8722708; cookiesPreferencesLogged=%7B%22userId%22%3A2321161890%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D',
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
        )->limit(10)->get();
    }

    public function getPagamentosHoje($period)
    {
        $client = new Client();

        switch ($period) {
            case 'hoje':
                $beginDate = Carbon::today()->toIso8601ZuluString();
                $endDate = Carbon::now()->toIso8601ZuluString();
                break;

            case '7dias':
                $beginDate = Carbon::now()->subDays(7)->toIso8601ZuluString();
                $endDate = Carbon::now()->toIso8601ZuluString();
                break;

            case '30dias':
                $beginDate = Carbon::now()->subDays(30)->toIso8601ZuluString();
                $endDate = Carbon::now()->toIso8601ZuluString();
                break;

            case 'todos':
                $beginDate = Carbon::create(2022, 1, 1)->toIso8601ZuluString();
                $endDate = Carbon::now()->toIso8601ZuluString();
                break;

            default:
                return response()->json(['erro' => 'Período inválido. Use: hoje, 7dias, 30dias ou todos'], 400);
        }

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
}
