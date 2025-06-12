<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function modulesView(){
        return view('modules');
    }

    public function newModuleView(){
        return view('newModule');
    }
}
