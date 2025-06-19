<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\StoreService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $storeService;
    private $posService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function dashboardView(){
        $storesCount = count( $this->storeService->getStores() );
        $posCount = count( $this->storeService->getPos() );
        return $this->storeService->getPixReceiptPdf(105408106474);

        return view('dashboard', compact('storesCount', 'posCount'));
    }
}
