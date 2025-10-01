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
        //return $storesData;
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

    public function newStoreView()
    {
        $moduleService = new ModuleService();
        $modulesData = $moduleService->getModules();

        return view('newStore', ['modules' => $modulesData]);
    }

    public function newStoreMercadoPago($nameStore, $endereco, $complemento, $cidade)
    {
        return $store = $this->StoreService->newStore(
            $nameStore,
            $endereco,
            $complemento,
            $cidade
        );
    }

    public function salesView()
    {
        $paymentsToday = $this->StoreService->getPaymentsToday();
        $paymentsSevenDays = $this->StoreService->getPaymentsSevenDaysInternal();
        $paymentsLast30Days = $this->StoreService->getPaymentsLast30Days();
        $allPayments = $this->StoreService->getAllPayments();
        //return $paymentsToday;
        return view('salesDetails', compact(
            'paymentsToday',
            'paymentsSevenDays',
            'paymentsLast30Days',
            'allPayments'
        ));
    }

    public function paymentView($id)
    {
        $paymentData = $this->StoreService->getPaymentInternalById($id);
        return view('payment', compact('paymentData'));
    }

    public function reversalData($id)
    {
        $this->StoreService->reversalAction($id);

        return redirect()->route('dashboard');
    }
}
