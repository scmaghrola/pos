<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\UserPermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ======================= Authentication Routes ========================
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// ======================= PayPal Token Route ===========================
Route::get('/paypal-token', function () {
    $clientId = env('PAY_PAL_CLIENT_ID');
    $clientSecret = env('PAY_PAL_CLIENT_SECRET');
    $baseUrl = env('PAY_PAL_BASE_URL');

    $response = Http::withBasicAuth($clientId, $clientSecret)
        ->asForm()
        ->post("$baseUrl/v1/oauth2/token", [
            'grant_type' => 'client_credentials',
        ]);

    return $response->json();
});

// ======================= Admin Routes ================================
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // ---------------- User Profile Routes -----------------
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');

    // ---------------- Test Auth -----------------
    Route::get('/test-auth', function () {
        return auth()->check() ? 'Logged in' : 'Not logged in';
    });

    // ---------------- Super Admin Routes -----------------
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/users/{id}/permissions', [UserPermissionController::class, 'edit'])
            ->name('users.permissions.edit');
        Route::post('/users/{id}/permissions', [UserPermissionController::class, 'update'])
            ->name('users.permissions.update');
    });

    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/list', [UserController::class, 'ajaxList'])->name('users.ajaxList');
    });



    // Users resource
    Route::resource('users', UserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'show' => 'users.show',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);


    // ---------------- POS Routes -----------------
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::resource('products', ProductsController::class);
        Route::post('products/upload-image', [ProductsController::class, 'uploadImage'])
            ->name('products.upload-image');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pos.dashboard');

    // POS AJAX Products
    Route::get('/pos/products', [PosController::class, 'getProducts'])->name('pos.products');

    // Products toggle status
    Route::patch('products/{product}/toggle-status', [ProductsController::class, 'toggleStatus'])
        ->name('pos.products.toggleStatus');

    // Products resource
    Route::resource('products', ProductsController::class)->names([
        'index' => 'pos.products.index',
        'create' => 'pos.products.create',
        'store' => 'pos.products.store',
        'show' => 'pos.products.show',
        'edit' => 'pos.products.edit',
        'update' => 'pos.products.update',
        'destroy' => 'pos.products.destroy',
    ])
        ->middleware([
            'index' => 'permission:products.view|products.edit|products.delete',
            'create' => 'permission:products.create',
            'store' => 'permission:products.create',
            'show' => 'permission:products.view',
            'edit' => 'permission:products.edit',
            'update' => 'permission:products.edit',
            'destroy' => 'permission:products.delete',
        ]);

    // Categories toggle status
    Route::patch('admin/categories/{category}/toggle-status', [CategoriesController::class, 'toggleStatus'])
        ->name('category.toggleStatus');

    // Categories resource
    Route::resource('admin/categories', CategoriesController::class)->names([
        'index' => 'category.list',
        'create' => 'category.add',
        'store' => 'categories.store',
        'show' => 'category.show',
        'edit' => 'category.edit',
        'update' => 'categories.update',
        'destroy' => 'categories.destroy',
    ]);

    // Customers toggle status
    Route::post('/customers/{id}/toggle-status', [CustomersController::class, 'toggleStatus'])
        ->name('customer.toggle');

    // Customers resource
    Route::resource('customers', CustomersController::class)->names([
        'index' => 'customer.list',
        'create' => 'customer.add',
        'store' => 'customer.store',
        'show' => 'customer.show',
        'edit' => 'customer.edit',
        'update' => 'customer.update',
        'destroy' => 'customer.destroy',
    ]);

    // POS Page resource
    Route::resource('pos-page', PosController::class)->names([
        'index' => 'pos-page.list',
        'create' => 'pos-page.add',
        'store' => 'pos-page.store',
        'show' => 'pos-page.show',
        'edit' => 'pos-page.edit',
        'update' => 'pos-page.update',
        'destroy' => 'pos-page.destroy',
    ]);

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.list');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.view');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.delete');
});
