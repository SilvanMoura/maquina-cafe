<?php

namespace App\Http\Controllers;

use App\Services\StoreService;
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
        return view('newStore');
    }

    public function newStore(Request $request){
        $request->validate([
            'cpfCnpjStore' => 'nullable|string',
            'nameStore' => 'nullable|string',
            'endereco' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cep' => 'nullable|string',
            'cidade' => 'nullable|string'
        ]);

        $store = Store::create([
            'nameStore' => $request->input('nome'),
            'cpfcnpj' => $cpf,
            'endereco' => $request->input('endereco'),
            'complemento' => $request->input('complemento'),
            'cep' => $request->input('cep'),
            'cidade' => $request->input('cidade'),
        ]);

        return response()->json(['message' => 'Loja criada com sucesso', 'registro' => $store], 201);
    }
}
