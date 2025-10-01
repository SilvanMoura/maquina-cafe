<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'dashboardView']);

//->middleware(['auth', 'verified'])->name('dashboard')

Route::get('/usuarios', [DashboardController::class, 'usuariosView']);
Route::get('/perfil', [DashboardController::class, 'perfilView']);
Route::put('/perfil/atualizar/{id}', [DashboardController::class, 'newPassword']);
Route::get('/usuarios/adicionar', [DashboardController::class, 'newUserView']);
Route::post('/usuarios/adicionar', [DashboardController::class, 'createUser']);
Route::put('/usuarios/atualizar/{id}', [DashboardController::class, 'updateUsers']);
Route::delete('/usuarios/delete/{id}', [DashboardController::class, 'deleteUsers']);

Route::get('/lojas', [StoreController::class, 'getStoreData']); //obtem todas as lojas
Route::get('/lojas/adicionar', [StoreController::class, 'newStoreView']);
Route::post('/lojas/adicionar', [StoreController::class, 'newStore']);
Route::get('/pos', [StoreController::class, 'getPosData']); //obtem todos os pontos de venda/caixa (pos)
Route::get('/vendas', [StoreController::class, 'salesView']);
Route::get('/pagamento/visualizar/{id}', [StoreController::class, 'paymentView']);
//Route::get('/pagamento/estorno/{id}', [StoreController::class, 'reversalData']);
Route::get('/pagamento/estorno', [StoreController::class, 'reversalView']);
Route::post('/pagamento/estorno', [StoreController::class, 'reversalData']);

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
Route::get('/notifications', [NotificationController::class, 'handleAlter']);


Route::post('/inter', [NotificationController::class, 'inter']);
Route::get('/gerarQr', [NotificationController::class, 'gerarQr']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
