<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\invoiceController;
use App\Http\Controllers\ArticleListeController;

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

Route::get('/invoice/show/{ref_purchase}', [invoiceController::class, 'show'])->name('invoice.show');
Route::get('/get_items_list_for_store', [ArticleListeController::class, 'getItemsListFromStore'])->name('get_items_list_for_store');
Route::get('/supply_liste', [ArticleListeController::class, 'supplyFlowListe'])->name('supply_liste');
Route::get('/sales_liste', [ArticleListeController::class, 'salesFlowList'])->name('sales_liste');
