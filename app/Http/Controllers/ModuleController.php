<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ModuleService;
use App\Models\Module;

class ModuleController extends Controller
{
    public function modulesView(){
        $moduleService = new ModuleService();
        $modulesData = $moduleService->getModules();

        //return $modulesData;
        return view('modules', ['modules' => $modulesData]);
        
    }

    public function newModuleView(){
        return view('newModule');
    }

    public function newModule(Request $request){
        $request->validate([
            'module' => 'nullable|string'
        ]);

        $newModule = new ModuleService();

        $responseBody = $newModule->newModule(
            $request->input('module')
        );

        return response()->json(['message' => 'Módulo criado com sucesso', 'registro' => $responseBody], 201);
    }

    public function couponsView(){
        $moduleService = new ModuleService();
        $couponsData = $moduleService->getCoupons();
        
        return view('coupons', ['coupons' => $couponsData]);
    }

    public function newCouponView(){
        return view('newCoupon');
    }

    public function newCoupon(Request $request){

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
}