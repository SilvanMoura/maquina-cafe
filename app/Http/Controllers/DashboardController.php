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
        $allPix = $this->storeService->getAllPix();

        $todaySales = $this->storeService->getPagamentosHoje('hoje');
        $sevenDaysSales = $this->storeService->getPagamentosHoje('7dias');
        $thirtyDaysSales = $this->storeService->getPagamentosHoje('30dias');
        $allSales = $this->storeService->getPagamentosHoje('todos');
        
        $todayCount = count($todaySales['results']);
        $sevenDaysCount = count($sevenDaysSales['results']);
        $thirtyDaysCount = count($thirtyDaysSales['results']);
        $allCount = count($allSales['results']);

        $todaySales = $this->storeService->valueTotal($todaySales);
        $sevenDaysSales = $this->storeService->valueTotal($sevenDaysSales);
        $thirtyDaysSales = $this->storeService->valueTotal($thirtyDaysSales);
        $allSales = $this->storeService->valueTotal($allSales);
        


        return view('dashboard', compact(
            'storesCount', 
            'posCount', 
            'allPix', 
            'todaySales', 
            'sevenDaysSales', 
            'thirtyDaysSales', 
            'allSales',
            'todayCount', 
            'sevenDaysCount', 
            'thirtyDaysCount', 
            'allCount'
        ));
    }
}
