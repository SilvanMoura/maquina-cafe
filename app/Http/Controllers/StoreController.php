<?php

namespace App\Http\Controllers;

use App\Services\StoreService;
use App\Models\Store;
use App\Services\ModuleService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    private $StoreService;

    public function __construct(StoreService $StoreService)
    {
        $this->StoreService = $StoreService;
    }

    public function getStoreData()
    {
        $storesData = $this->StoreService->getStores();
        
        return view('stores', ['storesData' => collect($storesData)]);
    }

    public function getPosData()
    {
        $posData = $this->StoreService->getPos();
        $storeIdName = $this->StoreService->getStoresById($posData['0']['store_id']);
        
        //$posData['0']['store_name'] =  $storeIdName;

        //return response()->json($posData);
        return view('pos', ['posData' => collect($posData)]);
    }

    public function consultOrderinPerson()
    {
        $posData = $this->StoreService->consultOrderinPerson(2321161890, 'mccf1030');

        return response()->json($posData);
        return view('pos', ['posData' => collect($posData)]);
    }

    public function newStoreView(){
        $moduleService = new ModuleService();
        $modulesData = $moduleService->getModules();
        
        return view('newStore', ['modules' => $modulesData]);
    }

    public function newStore(Request $request){

        $request->validate([
            'cpfCnpjStore' => 'nullable|string',
            'nameStore' => 'nullable|string',
            'endereco' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cep' => 'nullable|string',
            'cidade' => 'nullable|string',
            'modulo' => 'nullable|string'
        ]);

        $newStore = new StoreService();
        $newModule = new ModuleService();

        $responseBody = $newStore->newStore(
            $request->input('nameStore'),
            $request->input('endereco'),
            $request->input('complemento'),
            $request->input('cidade')
        );

        $addressLine = $responseBody['location']['address_line']; // Ex: "Rua Lázaro Zamenhof 56, Pelotas, Rio Grande do Sul, Brasil"

        // Separar os dados do endereço
        preg_match('/^(.*\d*)\,\s*(.*)\,\s*(.*)\,\s*Brasil$/', $addressLine, $matches);
        $endereco = $matches[1] ?? null;
        $cidade   = $matches[2] ?? null;
        $estado   = $matches[3] ?? null;

        $store = Store::create([
            'idStore'   => $responseBody['id'] ?? null,
            'nameStore' => $responseBody['name'] ?? null,
            'cpfcnpj'   => $request->input('cpfCnpjStore'),
            'endereco'  => $endereco,
            'cep'       => $request->input('cep'),
            'cidade'    => $cidade,
            'estado'    => $estado,
            'modulo'    => $request->input('modulo')
        ]);

        $data = json_decode($store, true);
        $newModule->registerStoreModule(
            $request->input('modulo'),
            $data['idStore']
        );

        $moduloValue = $newModule->getModuloById(
            $request->input('modulo'),
        );
        
        $dataPos = $newStore->newPos(
            $responseBody['id'],
            $responseBody['name'],
            $moduloValue
        );

        $physicalOrder = $newStore->physicalOrder(
            $responseBody['id'],
            $responseBody['name'],
            $moduloValue
        );

        return response()->json(['message' => 'Loja criada com sucesso', 'registro' => $store], 201);
    }
}
