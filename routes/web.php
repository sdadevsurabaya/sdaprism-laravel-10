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
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Role management
    Route::resource('roles', RoleController::class);

    // Brand
    Route::resource('brand', HeaderLogoController::class);

    // Currency
    Route::resource('currency', CurrencyController::class);

    // Users
    Route::resource('user', UserController::class);

    Route::post('/loginas/{id}', [AuthController::class, 'loginas'])->name('login.as');

});

// ========== UNTUK SEMUA ROLE YANG LOGIN ==========
Route::middleware(['auth'])->group(function () {
    // Pricelists
    Route::get('pricelists', [PricelistController::class, 'index'])->name('pricelists.index');
    Route::get('pricelists/create', [PricelistController::class, 'create'])->name('pricelists.create');
    Route::post('pricelists', [PricelistController::class, 'store'])->name('pricelists.store');
    Route::get('pricelists/{pricelist}/edit', [PricelistController::class, 'edit'])->name('pricelists.edit');
    Route::put('pricelists/{pricelist}', [PricelistController::class, 'update'])->name('pricelists.update');
    Route::delete('pricelists/{pricelist}', [PricelistController::class, 'destroy'])->name('pricelists.destroy');
    Route::get('pricelistPDF', [PricelistController::class, 'template_pdf'])->name('pricelist.template_pdf');
});

//quotation
Route::resource('quotation', QuotationController::class);
// Route::get('quotationCreate', [QuotationController::class, 'create'])->name('quotation.create');

Route::get('pricelist-table', [BackPriceListController::class, 'index'])->name('pricelist.table');
Route::get('pricelist-pdf/{pricelist}', [BackPriceListController::class, 'viewPdf'])->name('pricelist.pdf');

// Route::get('/', function () {return view('login');});
