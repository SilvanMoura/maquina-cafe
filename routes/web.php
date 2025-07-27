<?php

use Illuminate\Support\Facades\Route;
use BeyondCode\LaravelWebSockets\Facades\WebSocket;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DashboardController;
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

Route::get('/dashboard', [DashboardController::class, 'dashboardView']);

Route::get('/lojas', [StoreController::class, 'getStoreData']); //obtem todas as lojas
Route::get('/lojas/adicionar', [StoreController::class, 'newStoreView']);
Route::post('/lojas/adicionar', [StoreController::class, 'newStore']);
Route::get('/pos', [StoreController::class, 'getPosData']); //obtem todos os pontos de venda/caixa (pos)
Route::get('/vendas', [StoreController::class, 'salesView']);

Route::get('/modulos', [ModuleController::class, 'modulesView']); 
Route::get('/modulos/online', [ModuleController::class, 'modulesOnlineView']); 
Route::get('/modulos/adicionar', [ModuleController::class, 'newModuleView']);
Route::post('/modulos/adicionar', [ModuleController::class, 'newModule']);
Route::get('/cupons', [ModuleController::class, 'couponsView']);
Route::get('/cupons/adicionar', [ModuleController::class, 'newCouponView']);
Route::post('/cupons/adicionar', [ModuleController::class, 'newCoupon']);
Route::get('/readCode', [ModuleController::class, 'readCodeView']);
Route::post('/readCode', [ModuleController::class, 'depositCoupon']);
Route::get('/controle', [ModuleController::class, 'controlRemoteView']);
Route::post('/sendCommand', [ModuleController::class, 'sendCommandModule']);

//envia o credito do cupom para o modulo 

Route::get('/generateQrCode', [QrCodeController::class, 'qrCodeView']);
Route::get('/websocket', function () {
    return view('websocket');
});

Route::post('/qrcode', [QrCodeController::class, 'generate']);
Route::post('/notifications', [NotificationController::class, 'handle']);
