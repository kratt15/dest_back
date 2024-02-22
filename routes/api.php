<?php

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ManagerController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\NewPasswordController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:sanctum')->get('/logout', function (Request $request) {
    $cookie = Cookie::forget('jwt');
    return response([
        'message' => 'Deconnexion avec succes'
    ])->withCookie($cookie);
});

Route::post('/forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [NewPasswordController::class, 'resetPassword']);
Route::get('/test-mail', [TestController::class, 'testMail']);



// Route::middleware('auth:sanctum')->name('api.user')->group(function () {
//     Route::get('/user', [AuthController::class, 'user'])->name('user');
//     // Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
// });

Route::prefix('/')->controller(AuthController::class)->group(function () {

    Route::post('register', 'register');
    Route::post('login', 'login')->name('login');
});



// Route::middleware('auth')->group(function () {

// Route::prefix('/userss')->controller(UserController::class)->group(function () {
//     Route::post('/add', 'store');
// })->middleware('auth::sanctum');
// });






Route::middleware('auth:sanctum')->group(function () {



    Route::prefix('/customer')->controller(CustomerController::class)->group(function () {
        Route::get('/list', 'list')->middleware('role:user|admin|gerant');
        Route::post('/add', 'store')->middleware('role:admin');
        Route::put('/{customer}/edit', 'update')->middleware('role:admin');
        Route::delete('/{customer}/delete', 'delete')->middleware('role:admin');
        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    Route::prefix('/providers')->controller(ProviderController::class)->group(function () {
        Route::get('/list', 'list')->middleware('role:admin|user');
        Route::post('/add', 'store')->middleware('role:admin');
        Route::put('/{provider}/edit', 'update')->middleware('role:admin');
        Route::delete('/{provider}/delete', 'delete')->middleware('role:admin');
        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    Route::prefix('/users')->controller(UserController::class)->group(function () {
        Route::get('/list', 'list')->middleware('role:admin');
        Route::post('/add', 'store')->middleware('role:admin');
        Route::put('/{user}/edit', 'update')->middleware('role:admin');
        Route::delete('/{user}/delete', 'delete')->where(
            [
                'user' => '[0-9]+',

            ]
        )->middleware('role:admin');
        Route::put('/{user}/change_roles/{role}', 'Change_roles')->middleware('role:admin');
        Route::get('test', 'test')->middleware('role:admin');
        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });


    // ----- MAGASINS -----

    Route::prefix('/magasin')->controller(StoreController::class)->name('store.')->group(function () {
        Route::get('/list', 'list')->name('list')->middleware('role:admin|user|gerant');
        Route::get('/list_preview', 'listPreview')->middleware('role:admin|user|gerant');
        Route::post('/add', 'store')->middleware('role:admin');
        Route::put('/{store}/edit', 'update')->where(
            [
                'store' => '[0-9]+',
            ]
        )->middleware('role:admin');
        Route::delete('/{store}/delete', 'delete')->where(
            [
                'store' => '[0-9]+',
            ]
        )->middleware('role:admin');
        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    // ----- FIN MAGASINS -----


    // ----- GERANTS -----

    // Route::prefix('/gerant')->controller(ManagerController::class)->name('manager.')->group(function () {

    //     Route::get('/list', 'list')->name('list')->middleware('role:admin');
    //     Route::post('/add', 'store')->middleware('role:admin');
    //     Route::put('/{manager}/edit', 'update')->where(
    //         [
    //             'manager' => '[0-9]+',
    //         ]
    //     )->middleware('role:admin');
    //     Route::delete('/{manager}/delete', 'delete')->where(
    //         [
    //             'manager' => '[0-9]+',
    //         ]
    //     )->middleware('role:admin');
    //     Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    // });

    // ----- FIN GERANTS -----


    // ----- CATEGORIES -----

    Route::prefix('/categorie')->controller(CategoryController::class)->name('category.')->group(function () {

        Route::get('/list', 'list')->name('list')->middleware('role:admin|gerant');
        Route::post('/add', 'store')->middleware('role:admin');
        Route::put('/{category}/edit', 'update')->where(
            [
                'category' => '[0-9]+',
            ]
        )->middleware('role:admin');
        Route::delete('/{category}/delete', 'delete')->where(
            [
                'category' => '[0-9]+',
            ]
        )->middleware('role:admin');
        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    // ----- FIN CATEGORIES -----


    // ----- ARTICLES -----

    Route::prefix('/article')->controller(ItemController::class)->name('item.')->group(function () {
        Route::get('/list', 'list')->name('list')->middleware('role:admin|gerant|user');
        Route::get('/sort_ascending', 'sortAscendingList')->middleware('role:admin|gerant');
        Route::get('/sort_descending', 'sortDescendingList');
        Route::get('/most_sold_items', 'getMostSoldItems');
        Route::get('/sort_by_category', 'sortByCategory');
        Route::get('/sort_by_provider', 'sortByProvider');
        Route::get('/sort_by_store/{store}', 'sortByStore');
        Route::get('/supply_list', 'supplyList'); // Liste des approvisionnements par ordre décroissant
        Route::get('/sales_list', 'salesList'); // Liste des ventes par ordre décroissant
        Route::get('/open_sales_list', 'openSalesList'); // Liste des ventes non soldées par ordre croissant
        Route::get('/supply_and_sales_flow', 'supplyAndSalesFlowAsc'); // Liste des mouvements d'approvisionnement et vente par ordre croissant (par défaut)
        Route::get('/supply_and_sales_flow/sort_ascending', 'supplyAndSalesFlowAsc'); // Liste des mouvements d'approvisionnement et vente par ordre croissant
        Route::get('/supply_and_sales_flow/sort_descending', 'supplyAndSalesFlowDesc'); // Liste des mouvements d'approvisionnement et vente par ordre décroissant
        Route::post('/transfer_item/{store}', 'transfer'); // Transférer des articles d'un magasin à un autre
        Route::post('/add', 'store')->middleware('role:admin|gerant');
        Route::put('/{item}/edit', 'update')->where(
            [
                'item' => '[0-9]+',
            ]
        )->middleware('role:admin|gerant');
        Route::delete('/{item}/delete', 'delete')->where(
            [
                'item' => '[0-9]+',
            ]
        )->middleware('role:admin|gerant');

        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    // ----- FIN ARTICLES -----


    // ----- COMMANDES -----

    Route::prefix('/commande')->controller(OrderController::class)->name('order.')->group(function () {
        Route::get('/list', 'list')->name('list')->middleware('role:user|admin|gerant');
        Route::get('/accepted_orders_list', 'accepted_orders_list');
        Route::get('/unaccepted_orders_list', 'unaccepted_orders_list');
        Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
        Route::post('/add', 'store')->middleware('role:admin|gerant');
        Route::put('/{order}/edit', 'update')->where(
            [
                'order' => '[0-9]+',
            ]
        )->middleware('role:admin|gerant');
        Route::delete('/{order}/delete', 'delete')->where(
            [
                'order' => '[0-9]+',
            ]
        )->middleware('role:admin|gerant');

        Route::post('/accept_order/{order}', 'accept_order')->middleware('role:admin|gerant');
    });

    // ----- FIN COMMANDES -----


    // ----- ACHATS -----

    Route::prefix('/achat')->controller(PurchaseController::class)->name('purchase.')->group(function () {
        Route::get('/list', 'list')->name('list')->middleware('role:user|admin|gerant');
        Route::get('/daily_sales_list', 'dailySalesList')->middleware('role:user|admin|gerant');
        Route::post('/add', 'store')->middleware('role:admin|gerant');
        Route::put('/{purchase}/edit', 'update')->where(
            [
                'purchase' => '[0-9]+',
            ]
        )->middleware('role:admin|gerant');
        Route::delete('/{purchase}/delete', 'delete')->where(
            [
                'purchase' => '[0-9]+',
            ]
        )->middleware('role:admin|gerant');

        // Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    // ----- FIN ACHATS -----


    // ----- PAYEMENTS -----

    Route::prefix('/payement')->controller(PaymentController::class)->name('payment.')->group(function () {
        Route::get('/list', 'list')->name('list')->middleware('role:user|admin|gerant');
        Route::post('/add', 'store')->middleware('role:admin');
        Route::put('/{payment}/edit', 'update')->where(
            [
                'payment' => '[0-9]+',
            ]
        )->middleware('role:admin');
        Route::delete('/{payment}/delete', 'delete')->where(
            [
                'payment' => '[0-9]+',
            ]
        )->middleware('role:admin');
        Route::get('/achat/{ref_purchase}', 'listByRefPurchase')->name('list_by_ref_purchase')->middleware('role:user|admin|gerant');
        // Route::get('/search', 'search')->name('search')->middleware('role:admin|gerant|user');
    });

    // ----- FIN PAYEMENTS -----

});
