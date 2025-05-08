<?php

use App\Http\Controllers\Back\PriceListController as BackPriceListController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuotationController;


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
// Route::get('/', [HomeController::class, 'index']);
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

//pricelist
Route::get('pricelist', [PricelistController::class, 'index'])->name('pricelist');
Route::get('pricelistCreate', [PricelistController::class, 'create'])->name('pricelist.create');
Route::get('pricelistPDF', [PricelistController::class, 'template_pdf'])->name('pricelist.template_pdf');

//quotation
Route::get('quotation', [QuotationController::class, 'index'])->name('quotation');
Route::get('quotationCreate', [QuotationController::class, 'create'])->name('quotation.create');



Route::get('createUser', [UserController::class, 'index'])->name('createUser');

Route::get('pricelist-table', [BackPriceListController::class, 'index'])->name('pricelist.table');
Route::post('pricelist-pdf', [BackPriceListController::class, 'printPDF'])->name('pricelist.pdf');

// Route::get('/', function () {
//     return view('welcome');
// });
