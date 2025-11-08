<?php

use App\Models\Category;
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

Route::get('/', function () {
    return redirect()->route('login');
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
    Route::patch('categories/{category}/toggle-status', [CategoriesController::class, 'toggleStatus'])
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
    // categories.all
    Route::get('categories/all', [CategoriesController::class, 'getAll'])->name('categories.all');
    // categories.import
    Route::post('categories/import', [CategoriesController::class, 'import'])->name('categories.import');
    // categories.export
    Route::get('categories/export', [CategoriesController::class, 'export'])->name('categories.export');

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

// Route::get('/seed-categories', function () {
//     $categories = [
//         ["name" => "XYZ", "parent_category" => null, 'image' => 'xyz.png'],
//         ["name" => "ABC", "parent_category" => null, 'image' => 'abc.png'],
//         ["name" => "PQR", "parent_category" => null, 'image' => 'pqr.png'],
//         ["name" => "Sub XYZ 101", "parent_category" => "p1", 'image' => 'sub_xyz101.png'],
//         ["name" => "Sub XYZ 102", "parent_category" => "p1", 'image' => 'sub_xyz102.png'],
//         ["name" => "Sub XYZ 1", "parent_category" => "XYZ", 'image' => 'sub_xyz1.png'],
//         ["name" => "Sub XYZ 2", "parent_category" => "XYZ", 'image' => 'sub_xyz2.png'],
//         ["name" => "Sub ABC 1", "parent_category" => "ABC", 'image' => 'sub_abc1.png'],
//         ["name" => "Sub ABC 2", "parent_category" => "ABC", 'image' => 'sub_abc2.png'],
//         ["name" => "Sub PQR 1", "parent_category" => "PQR", 'image' => 'sub_pqr1.png'],
//         ["name" => "Sub PQR 2", "parent_category" => "PQR", 'image' => 'sub_pqr2.png'],
//         ["name" => "Sub PQR 101", "parent_category" => "p101", 'image' => 'sub_pqr1_101.png'],
//         ["name" => "Sub PQR 102", "parent_category" => "p102", 'image' => 'sub_pqr2_102.png'],
//     ];

//     $categoryMap = [];

    // foreach ($categories as $cat) {
    //     if ($cat['parent_category']) {
    //         // Check if parent category already exists or was created earlier
    //         $parent = $categoryMap[$cat['parent_category']] ??
    //             Category::firstOrCreate(
    //                 ['name' => $cat['parent_category'], 'parent_id' => null],
    //                 ['image' => 'default.png']
    //             );

    //         $category = Category::firstOrCreate(
    //             ['name' => $cat['name'], 'parent_id' => $parent->id],
    //             ['image' => $cat['image']]
    //         );

    //         $categoryMap[$cat['name']] = $category;
    //     } else {
    //         // Parent category (no parent_id)
    //         $category = Category::firstOrCreate(
    //             ['name' => $cat['name'], 'parent_id' => null],
    //             ['image' => $cat['image']]
    //         );

    //         $categoryMap[$cat['name']] = $category;
    //     }
    // }

//     return " Categories Seeded Successfully!";
// });
