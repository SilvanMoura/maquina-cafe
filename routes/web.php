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

Route::get('/usuarios', [DashboardController::class, 'usuariosView'])->middleware(['auth', 'verified']);
Route::get('/perfil', [DashboardController::class, 'perfilView'])->middleware(['auth', 'verified']);
Route::put('/perfil/atualizar/{id}', [DashboardController::class, 'newPassword'])->middleware(['auth', 'verified'])->name('newPassword');
Route::get('/usuarios/adicionar', [DashboardController::class, 'newUserView'])->middleware(['auth', 'verified']);
Route::post('/usuarios/adicionar', [DashboardController::class, 'createUser'])->middleware(['auth', 'verified']);
Route::put('/usuarios/atualizar/{id}', [DashboardController::class, 'updateUsers'])->middleware(['auth', 'verified'])->name('updateUsers');
Route::delete('/usuarios/delete/{id}', [DashboardController::class, 'deleteUsers'])->middleware(['auth', 'verified'])->name('deleteUsers');

Route::get('/lojas', [StoreController::class, 'getStoreData']); //obtem todas as lojas
Route::get('/lojas/adicionar', [StoreController::class, 'newStoreView'])->middleware(['auth', 'verified'])->name('tela adicionar loja');
Route::post('/lojas/adicionar', [StoreController::class, 'newStore'])->middleware(['auth', 'verified'])->name('tela adicionar loja');
Route::get('/pos', [StoreController::class, 'getPosData'])->middleware(['auth', 'verified']); //obtem todos os pontos de venda/caixa (pos)
Route::get('/vendas', [StoreController::class, 'salesView']);
Route::get('/pagamento/visualizar/{id}', [StoreController::class, 'paymentView']);
Route::get('/pagamento/estorno/{id}', [StoreController::class, 'reversalData']);

Route::get('/modulos', [ModuleController::class, 'modulesView']); 
Route::get('/modulos/online', [ModuleController::class, 'modulesOnlineView']); 
Route::get('/modulos/adicionar', [ModuleController::class, 'newModuleView'])->middleware(['auth', 'verified']);
Route::post('/modulos/adicionar', [ModuleController::class, 'newModule'])->middleware(['auth', 'verified']);
Route::get('/cupons', [ModuleController::class, 'couponsView']);
Route::get('/cupons/adicionar', [ModuleController::class, 'newCouponView']);
Route::post('/cupons/adicionar', [ModuleController::class, 'newCoupon']);
Route::get('/readCode', [ModuleController::class, 'readCodeView'])->middleware(['auth', 'verified']);
Route::post('/readCode', [ModuleController::class, 'depositCoupon'])->middleware(['auth', 'verified']);
Route::get('/controle', [ModuleController::class, 'controlRemoteView'])->middleware(['auth', 'verified']);
Route::post('/sendCommand', [ModuleController::class, 'sendCommandModule'])->middleware(['auth', 'verified']);

//envia o credito do cupom para o modulo 

Route::get('/generateQrCode', [QrCodeController::class, 'qrCodeView']);
Route::get('/websocket', function () {
    return view('websocket');
});

Route::post('/qrcode', [QrCodeController::class, 'generate']);
Route::post('/notifications', [NotificationController::class, 'handle']);


Route::post('/inter', [NotificationController::class, 'inter']);
Route::get('/gerarQr', [NotificationController::class, 'gerarQr']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
