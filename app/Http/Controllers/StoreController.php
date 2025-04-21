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
        $posData['0']['store_name'] =  $storeIdName;

        //return response()->json($posData);
        return view('pos', ['posData' => collect($posData)]);
    }

    public function consultOrderinPerson()
    {
        $posData = $this->StoreService->consultOrderinPerson(2321161890, 'mccf1030');

        return response()->json($posData);
        return view('pos', ['posData' => collect($posData)]);
    }
}
