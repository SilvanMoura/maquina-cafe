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


    /* public function getPixReceiptPdf($paymentId)
    {
        $client = new Client();

        $response = $client->get("https://www.mercadopago.com.br/money-out/transfer/api/receipt/pix_pdf/{$paymentId}/pix_account/pix_payment.pdf", [
            'headers' => [
		'cookie' => 'ftid=lfgV8PvFoGSSWOpBvcFMRn24tFTxXK1c-1748716757421; cookiesPreferencesNotLogged=%7B%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; _ga=GA1.1.1151310564.1750027010; _d2id=685dd283-c062-4275-aa01-8effbc1829d7; p_dsid=da33d370-eecc-4d0c-bdad-d4ba939f5336-1750033492551; _hjSessionUser_492923=eyJpZCI6ImY4MmE3MmIwLWYwNzYtNWIwYy05MWQ0LWVjZGQ4NTliMjI3OCIsImNyZWF0ZWQiOjE3NTAwMzM1NzM5ODIsImV4aXN0aW5nIjp0cnVlfQ==; dxDevPanelOnboarding=true; mp_dx-dev-panel-production-product=true; dsid=0fefc014-9782-40cb-899c-87b1231598a9-1750037809126; _hjSessionUser_4992954=eyJpZCI6IjFjNDQ0NTZiLTBhMjYtNWEzMi1iZWY2LTlmZmE5ZTdlMmJkMiIsImNyZWF0ZWQiOjE3NTAwMzM5MjEzNjgsImV4aXN0aW5nIjp0cnVlfQ==; edsid=6d74e3a5-7cf6-36be-998f-1383cebf5ce5-1750041657073; _tt_enable_cookie=1; _ttp=01JZGKKZ7YCQWRDASWKDH1J19Z_.tt.2; ttcsid_CU409KRC77U5T1OVP8KG=1751830363395::StSXEKCYzznSrXXdndHW.1.1751830364394; ttcsid_CUV1O83C77UCOV2E0PVG=1751830363395::6uLXzsd9GcsdY9GHGX8i.1.1751830364394; ttcsid_CUN021JC77U74NKAGB7G=1751830363396::3Bsvh38eBmMzgcTyeyFA.1.1751830364394; ttcsid_CUJ1CJ3C77U09FJDRBMG=1751830363396::SgtGHiWNL28jMlUO8lIV.1.1751830364394; ttcsid_CVDCTDJC77UCRE2PIO20=1751830363396::dUIjZALuDsxEEY-U5aJp.1.1751830364395; ttcsid_D04F33RC77U7UBOAPF1G=1751830363397::cMkN1xl2HklUe-Kpw8H7.1.1751830364395; orgnickp=DHGCBDEAF12717; orguseridp=2710523715; dxDevPanelAppCollaboratorsModal=true; mp_spending-tracking_walkthrough={"value":"finished","date":"2025-10-02"}; __rtbh.uid=%7B%22eventType%22%3A%22uid%22%2C%22id%22%3A%222710523715%22%2C%22expiryDate%22%3A%222026-10-03T00%3A00%3A17.467Z%22%7D; __rtbh.lid=%7B%22eventType%22%3A%22lid%22%2C%22id%22%3A%22b8o6zYPxUCCk7wMFFdAT%22%2C%22expiryDate%22%3A%222026-10-03T00%3A00%3A17.467Z%22%7D; ttcsid_C9SJ5SBC77UADFMAH8T0=1759449617669::J2pgi6rIX9MVhFAz_5-0.1.1759449617669.0; _uetvid=f22ee2309feb11f0a11e895521b5b58c; ttcsid=1759449617671::0Ez_aEZJiND5YwqbtNJD.2.1759449645522.0; ttcsid_CFVSC2JC77U0ARCJTCJ0=1759449617671::R3BJEXdVCOgU_sOWaHqs.1.1759449645523.0; cookiesPreferencesLoggedFallback=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; QSI_SI_dgmzu5WdT6dFzHU_intercept=true; iaResourcesTooltip=true; QSI_SI_80KyLvRlU9bMumG_intercept=true; _ga_XJRJL56B0Z=GS2.1.s1768798146$o40$g1$t1768798540$j1$l0$h0; _csrf=y8O1c0MdLcwUyirGaJbqL7S1; QSI_HistorySession=https%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1768922658693%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1768955970269%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1768968579312; cookiesPreferencesLogged=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; _mldataSessionId=d3550247-035b-490f-9500-ecbf703fc684; orguserid=9Zh90hd9ZHh09; mp_wsid=eyJhbGciOiJSUzI1NiIsImtpZCI6IlBNREhlSGt2WEdPZ2JmWFNXZ2VnMDRzeEVTZG1yV0N1TndKcFl1N3lGUjg9IiwidHlwIjoiSldUIn0.eyJhZG1pbl9vdHAiOmZhbHNlLCJhdXRoX2Zsb3ciOiJhY2Nlc3MiLCJjbGllbnRfaWQiOjY0OTUyMTMwOTkwNDY1NywiZXhwIjoxODYzNzI5MTczLCJpYXQiOjE3NjkwMzQ3NzMsImlzcyI6InVybjphdXRoLXNlcnZlcjpzZXNzaW9ucyIsImp0aSI6IjdkYzJiZjE1LWNkNWUtNDQzOS05Y2ZmLTliODllYzIwYmIyZSIsInByb2R1Y3RfaWQiOjIsInNpdGVfaWQiOiJtbGIiLCJzc2kiOiI1MmQ4OGZlMS0wYTNjLTQ0ZDQtOTgxMC1lYjljMGFlNTI1YjAiLCJzdWIiOiJ1cm46dXNlcnM6MjcxMDUyMzcxNSJ9.us-b6vTHMfn9yd1LBKKbsZ4OeRtq-9jNh1LCn0_VTxDCKbguffPhqsRPlfeO0eLr3TDe-IdXxOG_Ouv9dhuXVB16IK0bKhG2iT5FVvARWEL7fMPseotANHkYoRXIrYdKg12ZdmwU95781Hvf6csiOxBnih7UO8Fw013HqfR3pOXiz7fVXBqgVFvXncZ4HRIBdAM3JWTkhEoS5uGsHr5OyNSW9gcKo-QWs5rLCKGIUhEgQNqqLpV26NPe6GXOAybNAynizIdx3kI-nlyV7Zw33vJpVfijzd0VIxCzRbZw6HB35051alp24zMLZ10linWzKmmUPsAjvu0dPNRJ7LecKg; ssid=ghy-012118-FmYbNmnwWQLmBnICQufhPRquV67ujg-__-2710523715-__-1863729173091--RRR_0-RRR_0; app-theme=yellowblue-light; hide-cookie-banner=2710523715-COOKIE_PREFERENCES_ALREADY_SET; ttl=1769034775006; p_edsid=caf493e4-8911-35b3-90da-264364477302-1769034776152; x-meli-session-id=armor.bbc9cc7cad5578b1b33770140d8f086363fa917e4e0c56e819654153579f36e6b004a93ced9b878d01c7180c94393f3832a3b2f8cd1aefe5d4bf447530e78e4d8088428a6a3af62ecda7330318f2acfdb430531bb9bdbc14f85d6cf277ff1a6e.f0a1a8a3a0ccada91688b8bf23b2c01f; nsa_rotok=eyJhbGciOiJSUzI1NiIsImtpZCI6IjMiLCJ0eXAiOiJKV1QifQ.eyJpZGVudGlmaWVyIjoiNTJkODhmZTEtMGEzYy00NGQ0LTk4MTAtZWI5YzBhZTUyNWIwIiwicm90YXRpb25faWQiOiI3MGYwODUwOC1lMDQzLTQ3NmEtYmJhYS05YWI3ZTFjMTFiMWIiLCJwbGF0Zm9ybSI6Ik1QIiwicm90YXRpb25fZGF0ZSI6MTc2OTAzNTM3MywiZXhwIjoxNzcxNjI2NzczLCJqdGkiOiJhMjY3OGMxYS1jZjBkLTQ5N2UtYjY4YS04N2JiMDRiZDNjNjUiLCJpYXQiOjE3NjkwMzQ3NzMsInN1YiI6IjUyZDg4ZmUxLTBhM2MtNDRkNC05ODEwLWViOWMwYWU1MjViMCJ9.Sy2QJmdDvDCAB63LAFiQphzpSkCuZ1JfwPwcicbjuWfpq6rVNsO_GY9iejdv_LJeVJBvquDEIBNgD76ApRcd5YcgK7TUVqSnb4jqfXDpwThsQKg8NCPZiImjrS4r1pXVnVy4_2_ArYgUByT5Fl3HNrj71FdqD8ZLOPacgLssHqiQNSLjfEqttP5oOo8ZdK6BAeYoB0fXfazoMWRv2FmSgfIRlNQ5k6Qd2iooIRKN3zLw4F0Y4Xn0gNkhOz8mwWz3ToPzam9f4XLri53YYRilWvkn-EAhReDyenvi3GPLtxxDbldhqF2nBukaWJerG_5nrrmYc7edP5i4BpSUJTEEdg; app-restyled=false',
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
    } */

    public function getPixReceiptPdf($paymentId)
    {
        $client = new Client();

        $response = $client->get(
            "https://www.mercadopago.com.br/money-out/transfer/api/receipt/pix_pdf/{$paymentId}/pix_account/pix_payment.pdf",
            [
                'headers' => [
                    'cookie' => 'ftid=lfgV8PvFoGSSWOpBvcFMRn24tFTxXK1c-1748716757421; cookiesPreferencesNotLogged=%7B%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; _ga=GA1.1.1151310564.1750027010; _d2id=685dd283-c062-4275-aa01-8effbc1829d7; p_dsid=da33d370-eecc-4d0c-bdad-d4ba939f5336-1750033492551; _hjSessionUser_492923=eyJpZCI6ImY4MmE3MmIwLWYwNzYtNWIwYy05MWQ0LWVjZGQ4NTliMjI3OCIsImNyZWF0ZWQiOjE3NTAwMzM1NzM5ODIsImV4aXN0aW5nIjp0cnVlfQ==; dxDevPanelOnboarding=true; mp_dx-dev-panel-production-product=true; dsid=0fefc014-9782-40cb-899c-87b1231598a9-1750037809126; _hjSessionUser_4992954=eyJpZCI6IjFjNDQ0NTZiLTBhMjYtNWEzMi1iZWY2LTlmZmE5ZTdlMmJkMiIsImNyZWF0ZWQiOjE3NTAwMzM5MjEzNjgsImV4aXN0aW5nIjp0cnVlfQ==; edsid=6d74e3a5-7cf6-36be-998f-1383cebf5ce5-1750041657073; _tt_enable_cookie=1; _ttp=01JZGKKZ7YCQWRDASWKDH1J19Z_.tt.2; ttcsid_CU409KRC77U5T1OVP8KG=1751830363395::StSXEKCYzznSrXXdndHW.1.1751830364394; ttcsid_CUV1O83C77UCOV2E0PVG=1751830363395::6uLXzsd9GcsdY9GHGX8i.1.1751830364394; ttcsid_CUN021JC77U74NKAGB7G=1751830363396::3Bsvh38eBmMzgcTyeyFA.1.1751830364394; ttcsid_CUJ1CJ3C77U09FJDRBMG=1751830363396::SgtGHiWNL28jMlUO8lIV.1.1751830364394; ttcsid_CVDCTDJC77UCRE2PIO20=1751830363396::dUIjZALuDsxEEY-U5aJp.1.1751830364395; ttcsid_D04F33RC77U7UBOAPF1G=1751830363397::cMkN1xl2HklUe-Kpw8H7.1.1751830364395; orgnickp=DHGCBDEAF12717; orguseridp=2710523715; dxDevPanelAppCollaboratorsModal=true; mp_spending-tracking_walkthrough={"value":"finished","date":"2025-10-02"}; __rtbh.uid=%7B%22eventType%22%3A%22uid%22%2C%22id%22%3A%222710523715%22%2C%22expiryDate%22%3A%222026-10-03T00%3A00%3A17.467Z%22%7D; __rtbh.lid=%7B%22eventType%22%3A%22lid%22%2C%22id%22%3A%22b8o6zYPxUCCk7wMFFdAT%22%2C%22expiryDate%22%3A%222026-10-03T00%3A00%3A17.467Z%22%7D; ttcsid_C9SJ5SBC77UADFMAH8T0=1759449617669::J2pgi6rIX9MVhFAz_5-0.1.1759449617669.0; _uetvid=f22ee2309feb11f0a11e895521b5b58c; ttcsid=1759449617671::0Ez_aEZJiND5YwqbtNJD.2.1759449645522.0; ttcsid_CFVSC2JC77U0ARCJTCJ0=1759449617671::R3BJEXdVCOgU_sOWaHqs.1.1759449645523.0; cookiesPreferencesLoggedFallback=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; QSI_SI_dgmzu5WdT6dFzHU_intercept=true; iaResourcesTooltip=true; QSI_SI_80KyLvRlU9bMumG_intercept=true; _csrf=y8O1c0MdLcwUyirGaJbqL7S1; orguserid=9Zh90hd9ZHh09; mp_wsid=eyJhbGciOiJSUzI1NiIsImtpZCI6IlBNREhlSGt2WEdPZ2JmWFNXZ2VnMDRzeEVTZG1yV0N1TndKcFl1N3lGUjg9IiwidHlwIjoiSldUIn0.eyJhZG1pbl9vdHAiOmZhbHNlLCJhdXRoX2Zsb3ciOiJhY2Nlc3MiLCJjbGllbnRfaWQiOjY0OTUyMTMwOTkwNDY1NywiZXhwIjoxODYzNzI5MTczLCJpYXQiOjE3NjkwMzQ3NzMsImlzcyI6InVybjphdXRoLXNlcnZlcjpzZXNzaW9ucyIsImp0aSI6IjdkYzJiZjE1LWNkNWUtNDQzOS05Y2ZmLTliODllYzIwYmIyZSIsInByb2R1Y3RfaWQiOjIsInNpdGVfaWQiOiJtbGIiLCJzc2kiOiI1MmQ4OGZlMS0wYTNjLTQ0ZDQtOTgxMC1lYjljMGFlNTI1YjAiLCJzdWIiOiJ1cm46dXNlcnM6MjcxMDUyMzcxNSJ9.us-b6vTHMfn9yd1LBKKbsZ4OeRtq-9jNh1LCn0_VTxDCKbguffPhqsRPlfeO0eLr3TDe-IdXxOG_Ouv9dhuXVB16IK0bKhG2iT5FVvARWEL7fMPseotANHkYoRXIrYdKg12ZdmwU95781Hvf6csiOxBnih7UO8Fw013HqfR3pOXiz7fVXBqgVFvXncZ4HRIBdAM3JWTkhEoS5uGsHr5OyNSW9gcKo-QWs5rLCKGIUhEgQNqqLpV26NPe6GXOAybNAynizIdx3kI-nlyV7Zw33vJpVfijzd0VIxCzRbZw6HB35051alp24zMLZ10linWzKmmUPsAjvu0dPNRJ7LecKg; ssid=ghy-012118-FmYbNmnwWQLmBnICQufhPRquV67ujg-__-2710523715-__-1863729173091--RRR_0-RRR_0; _ml_ar-browser-check=130d98cf-6f29-47dd-9652-07214385e5a8; x-theme=andes_legacy-1.0.0-light; _ga_XJRJL56B0Z=GS2.1.s1769379726$o42$g0$t1769380352$j60$l0$h0; app-theme=yellowblue-light; cookiesPreferencesLogged=%7B%22userId%22%3A2710523715%2C%22categories%22%3A%7B%22advertising%22%3Atrue%2C%22functionality%22%3Atrue%2C%22performance%22%3Atrue%2C%22traceability%22%3Atrue%7D%7D; hide-cookie-banner=2710523715-COOKIE_PREFERENCES_ALREADY_SET; _mldataSessionId=bfd38cd9-5dba-4504-80f2-ca576981e03d; ttl=1769526711364; QSI_HistorySession=https%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1768955970269%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1768968579312%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1769379525548%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fsavings%2Fdetail%2F03645669-1bd8-4665-90e3-6a4d6ac07799%3Frefer%3Dhub~1769379551566%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1769379594264%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1769379713913%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1769380489757%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1769385896496%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1769467277950%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1769468451104%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1769471927112%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome~1769511870578%7Chttps%3A%2F%2Fwww.mercadopago.com.br%2Fhome%23from-section%3Dmenu~1769526978101; p_edsid=5901cbd8-52bf-3947-b236-10a29164960c-1769526713624; x-meli-session-id=armor.2baeddf956b867f0d524c2f4d4b27b10cfc0c4f1d8d363324e8c36f52d6b22f185ec0d9ed881eb141bd41e7269cdb9476e75e3a2cce4e637b7bb52ec395ef5b262b46fee2e0fe249a38c975b69a058901b0c4816f3fbc95ebba696d06b87a961.0105bc14d3eee7bc95f0c79e9b740f77; nsa_rotok=eyJhbGciOiJSUzI1NiIsImtpZCI6IjMiLCJ0eXAiOiJKV1QifQ.eyJpZGVudGlmaWVyIjoiNTJkODhmZTEtMGEzYy00NGQ0LTk4MTAtZWI5YzBhZTUyNWIwIiwicm90YXRpb25faWQiOiI3YTI4M2Q0Yy04ZTczLTRiMWYtODE3OC0yMThmYTA4N2YwOGMiLCJwbGF0Zm9ybSI6Ik1QIiwicm90YXRpb25fZGF0ZSI6MTc2OTUyNzMwNywiZXhwIjoxNzcyMTE4NzA3LCJqdGkiOiJmYTdmOTI4OC1lMjBkLTQ2MmItYjFjYS01MjJlMDJjYzI5ZTAiLCJpYXQiOjE3Njk1MjY3MDcsInN1YiI6IjUyZDg4ZmUxLTBhM2MtNDRkNC05ODEwLWViOWMwYWU1MjViMCJ9.hS0uG7BDV1c0SLsz1Bq2MB2DuLBpO0RN2AZbfpJQrerJeXWIQZOWoH2hz1fEk8Wwx4ySn_Fq6ePFhXq4UClH6YMTCYTOr4a4HrNTC2OaE5sRIgaTj5yTmcUmrTTYpYYXR8CQiYFCFWd-PxOVpve6GT8W8azw953-LbpsGjvcPvp11zVB0si05DvO7kiWyUaVO1FnChW7eeEoQXjGM3vEM1A685b3xs4aDpD81F3dIZZZW13Fo6Q_I6bhCQ9zt6sJnxni21qWVTci5ByGPlO0ifLgJrOE0qr-92EBgYmDbrEERMl58aTMPYGfouaHljBjAShnWmI5AboKrO3_nYg2NQ',
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
