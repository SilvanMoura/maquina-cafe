<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ModuleService;
use App\Services\StoreService;
use App\Models\Module;

class ModuleController extends Controller
{
    public function modulesView()
    {
        $moduleService = new ModuleService();
        $modulesData = $moduleService->getModules();

        //return $modulesData;
        return view('modules', ['modules' => $modulesData]);
    }

    public function newModuleView()
    {
        return view('newModule');
    }

    public function newModule(Request $request)
    {
        $request->validate([
            'module' => 'nullable|string'
        ]);

        $newModule = new ModuleService();

        $responseBody = $newModule->newModule(
            $request->input('module')
        );

        return response()->json(['message' => 'Módulo criado com sucesso', 'registro' => $responseBody], 201);
    }

    public function couponsView()
    {
        $moduleService = new ModuleService();
        $couponsData = $moduleService->getCoupons();

        return view('coupons', ['coupons' => $couponsData]);
    }

    public function newCouponView()
    {
        return view('newCoupon');
    }

    public function newCoupon(Request $request)
    {

        $request->validate([
            'name' => 'nullable|string',
            'value' => 'nullable|string',
            'telefone' => 'nullable|string'
        ]);

        $newModule = new ModuleService();

        $responseBody = $newModule->newCoupon(
            $request->input('name'),
            $request->input('value'),
            $request->input('telefone')
        );

        return response()->json(['message' => 'Cupom Criado com sucesso', 'registro' => $responseBody], 201);
    }

    public function readCodeView(Request $request)
    {
        $cupomId = $request->query('id');
        return view('readCode', compact('cupomId'));
    }

    public function depositCoupon(Request $request)
    {
        $coupon = new ModuleService();
        $store = new StoreService();

        $couponData = $coupon->getCouponsById($request->input('cupom_id'));

        $posData = $store->getPosById($request->input('pos_id'));
        $idModulo = $posData['external_id'];

        $couponStatus = $coupon->deactivatingCoupon($couponData[0]['id']);

        $coupon->sendCredits($idModulo, $couponData['value']);
        
        return response()->json([
            'status' => 'Cupom enviado com sucesso!',
            'success' => true
        ]);
    }

    public function controlRemoteView()
    {
        $modulesUse = new ModuleService();
        $modulesData = $modulesUse->getModulesUse();
        return view('controlRemote', compact('modulesData'));
    }

    public function sendCommandModule(Request $request)
    {
        $moduloId = $request->input('modulo');
        $button = $request->input('botao');

        $moduleSend = new ModuleService();

        return $moduleSend->sendCommandToButton($moduloId, $button);
        return $request;
    }
}
