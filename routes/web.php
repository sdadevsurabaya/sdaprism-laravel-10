<?php

use App\Http\Controllers\Back\PriceListController as BackPriceListController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HeaderLogoController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;

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

// Route::get('/', [HomeController::class, 'index']);
Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

//pricelist
Route::get('pricelists', [PricelistController::class, 'index'])->name('pricelists.index');
Route::get('pricelists/create', [PricelistController::class, 'create'])->name('pricelists.create');
Route::post('pricelists', [PricelistController::class, 'store'])->name('pricelists.store');
Route::get('pricelists/{pricelist}/edit', [PricelistController::class, 'edit'])->name('pricelists.edit');
Route::put('pricelists/{pricelist}', [PricelistController::class, 'update'])->name('pricelists.update');
Route::delete('pricelists/{pricelist}', [PricelistController::class, 'destroy'])->name('pricelists.destroy');
Route::get('pricelistPDF', [PricelistController::class, 'template_pdf'])->name('pricelist.template_pdf');

//quotation
Route::resource('quotation', QuotationController::class);
// Route::get('quotationCreate', [QuotationController::class, 'create'])->name('quotation.create');

// Roles
Route::resource('roles', RoleController::class);

// BRAND
Route::resource('brand', HeaderLogoController::class);

// CURRENCY
Route::resource('currency', CurrencyController::class);

Route::resource('user', UserController::class);

Route::get('pricelist-table', [BackPriceListController::class, 'index'])->name('pricelist.table');
Route::get('pricelist-pdf/{pricelist}', [BackPriceListController::class, 'viewPdf'])->name('pricelist.pdf');

// Route::get('/', function () {return view('login');});
