<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Back\PriceListController as BackPriceListController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HeaderLogoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.submit');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
// ========== HANYA UNTUK ADMIN ==========
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Role management
    Route::resource('roles', RoleController::class);

    // Brand
    Route::resource('brand', HeaderLogoController::class);

    // Currency
    Route::resource('currency', CurrencyController::class);

    // Users
    Route::post('user/whitelist-realtime', [UserController::class, 'updateWhitelist'])->name('user.update-whitelist');
    Route::resource('user', UserController::class);

    Route::post('/loginas/{id}', [AuthController::class, 'loginas'])->name('login.as');


    // Activity Log (Admin Only)
    Route::get('/activity-log', [\App\Http\Controllers\Back\ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/{id}', [\App\Http\Controllers\Back\ActivityLogController::class, 'show'])->name('activity-log.show');

});

// ========== UNTUK SEMUA ROLE YANG LOGIN ==========
Route::middleware(['auth'])->group(function () {
    // Dashboard Utama
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Pricelists
    Route::get('pricelists/realtime', [PricelistController::class, 'realtime'])->name('pricelists.realtime');
    Route::get('pricelists', [PricelistController::class, 'index'])->name('pricelists.index');
    Route::get('pricelists/create', [PricelistController::class, 'create'])->name('pricelists.create');
    Route::get('pricelists/{pricelist}', [PricelistController::class, 'show'])->name('pricelists.show');

    Route::post('pricelists', [PricelistController::class, 'store'])->name('pricelists.store');
    Route::get('pricelists/{pricelist}/edit', [PricelistController::class, 'edit'])->name('pricelists.edit');
    Route::put('pricelists/{pricelist}', [PricelistController::class, 'update'])->name('pricelists.update');
    Route::delete('pricelists/{pricelist}', [PricelistController::class, 'destroy'])->name('pricelists.destroy');
    Route::get('pricelistPDF', [PricelistController::class, 'template_pdf'])->name('pricelist.template_pdf');

    // QR Scan Alias & API
    Route::get('/back/scan-qr', [\App\Http\Controllers\Back\ScanController::class, 'index'])->name('scan.qr');
    Route::get('/back/camera-scan', [\App\Http\Controllers\Back\ScanController::class, 'camera'])->name('scan.camera');
    Route::get('/back/rakitan-data', [\App\Http\Controllers\Back\RakitanApiController::class, 'getData'])->name('rakitan.data');
    Route::get('/back/pricelist-bridge-data', [\App\Http\Controllers\Back\PriceListBridgeApiController::class, 'getData'])->name('pricelist.bridge.data');

});

//quotation
Route::resource('quotation', QuotationController::class);
// Route::get('quotationCreate', [QuotationController::class, 'create'])->name('quotation.create');

Route::get('pricelist-table', [BackPriceListController::class, 'index'])->name('pricelist.table');
Route::get('pricelist-pdf/{pricelist}', [BackPriceListController::class, 'viewPdf'])->name('pricelist.pdf');

// Route::get('/', function () {return view('login');});
