<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PricelistController;
use App\Http\Controllers\UserController;

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


Route::get('pricelist', [PricelistController::class, 'index'])->name('pricelist');

Route::get('createUser', [UserController::class, 'index'])->name('createUser');

Route::get('/pricelist-table', [App\Http\Controllers\Back\PriceListController::class, 'index'])->name('pricelist.table');

// Route::get('/', function () {
//     return view('welcome');
// });
