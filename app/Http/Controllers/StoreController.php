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
        
        //return response()->json($storesData);
        
        return view('pos', ['posData' => collect($posData)]);
    }
}
