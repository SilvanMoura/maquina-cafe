<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ModuleService;
use App\Services\StoreService;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    private $StoreService;

    public function __construct(StoreService $StoreService)
    {
        $this->StoreService = $StoreService;
    }

    public function modulesView()
    {
        $moduleService = new ModuleService();

        $userId = Auth::id();

        $userData = $this->StoreService->getUsersById($userId);

        if ($userData->level == '3') {
            $modulesData = $moduleService->getModulesUseById($userId);
        } else {
            $modulesData = $moduleService->getModulesUse();
        }

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

        $idModulo = $store->getModuloByMercadoPagoId($request->input('pos_id'));
        $idModulo = $coupon->getModuloById($idModulo);

        $couponStatus = $coupon->deactivatingCoupon($couponData[0]['id']);

        $module = new ModuleService();
        //$idModulo = $module->getModuloById($idModulo);

        $coupon->sendCredits($idModulo, $couponData['value']);

        return response()->json([
            'status' => 'Cupom enviado com sucesso!',
            'success' => true
        ]);
    }

    public function sendPulses(Request $request)
    {
        $request->validate([
            'modulo'   => 'required',
            'creditos' => 'required|integer|min:1',
        ]);

        $module = new ModuleService();

        $idModulo = $request->modulo;
        $pulses = $request->creditos;

        // Aqui você envia para o MQTT
        $module->sendCredits($idModulo, $pulses);

        return response()->json([
            'success' => true,
            'message' => 'Créditos enviados com sucesso.'
        ]);
    }

    public function controlRemoteView()
    {
        $modulesUse = new ModuleService();

        $userId = Auth::id();

        $userData = $this->StoreService->getUsersById($userId);

        if ($userData->level == '3') {
            $modulesData = $modulesUse->getModulesUseById($userId);
        } else {
            $modulesData = $modulesUse->getModulesUse();
        }
        //return $modulesData;
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

    public function modulesOnlineView()
    {
        $moduleService = new ModuleService();
        $modulesData = $moduleService->getModulesOnline();

        //return $modulesData;
        return view('modulesOnline', ['modules' => $modulesData]);
    }
}
