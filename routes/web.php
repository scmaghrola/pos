<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;

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

Route::prefix('admin')->group(function () {



    Route::prefix('pos')->name('pos.')->group(function () {
        Route::resource('products', ProductsController::class);
        Route::post('products/upload-image', [ProductsController::class, 'uploadImage'])->name('products.upload-image');
    });


    Route ::get('/dashboard',[DashboardController::class,'index'])->name('pos.dashboard');


    Route::patch('products/{product}/toggle-status', [ProductsController::class, 'toggleStatus'])
        ->name('pos.products.toggleStatus');
    Route::resource('products', ProductsController::class)->names([
        'index' => 'pos.products.index',
        'create' => 'pos.products.create',
        'store' => 'pos.products.store',
        'show' => 'pos.products.show',
        'edit' => 'pos.products.edit',
        'update' => 'pos.products.update',
        'destroy' => 'pos.products.destroy',
    ]);



    Route::patch('admin/categories/{category}/toggle-status', [CategoriesController::class, 'toggleStatus'])
        ->name('category.toggleStatus');
    Route::resource('admin/categories', CategoriesController::class)->names([
        'index' => 'category.list',
        'create' => 'category.add',
        'store' => 'categories.store',
        'show' => 'category.show',
        'edit' => 'category.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);


    Route::post('/customers/{id}/toggle-status', [CustomersController::class, 'toggleStatus'])->name('customer.toggle');
    Route::resource('customers', CustomersController::class)->names([
        'index' => 'customer.list',
        'create' => 'customer.add',
        'store' => 'customer.store',
        'show' => 'customer.show',
        'edit' => 'customer.edit',
        'update' => 'customer.update',
        'destroy' => 'customer.destroy',
    ]);

    Route::resource('pos-page', PosController::class)->names([
        'index' => 'pos-page.list',
        'create' => 'pos-page.add',
        'store' => 'pos-page.store',
        'show' => 'pos-page.show',
        'edit' => 'pos-page.edit',
        'update' => 'pos-page.update',
        'destroy' => 'pos-page.destroy',
    ]);

    Route::get('orders', [OrderController::class, 'index'])->name('orders.list');

    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.view');

    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.delete');
});
