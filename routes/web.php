<?php

use Illuminate\Support\Facades\Route;
use BeyondCode\LaravelWebSockets\Facades\WebSocket;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    //return view('qrCodeGenerate');
    return view('dashboard');
});

Route::get('/lojas', [StoreController::class, 'getStoreData']); //obtem todas as lojas
Route::get('/lojas/adicionar', [StoreController::class, 'newStoreView']);
Route::post('/lojas/adicionar', [StoreController::class, 'newStore']);

Route::get('/pos', [StoreController::class, 'getPosData']); //obtem todos os pontos de venda/caixa (pos)

Route::get('/modulos', [ModuleController::class, 'modulesView']); 
Route::get('/modulos/adicionar', [StoreController::class, 'newModuleView']);

Route::get('/generateQrCode', [QrCodeController::class, 'qrCodeView']);
Route::get('/websocket', function () {
    return view('websocket');
});

Route::post('/qrcode', [QrCodeController::class, 'generate']);
Route::post('/notifications', [NotificationController::class, 'handle']);
