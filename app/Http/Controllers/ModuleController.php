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
}
