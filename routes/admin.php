<?php
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleChangeController;
use Illuminate\Support\Facades\Route;

//Admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/home', [AdminDashboardController::class, 'index'])->name('adminDashboard');

    //Category
    Route::prefix('category')->group(function () {
        Route::get('list', [CategoryController::class, 'list'])->name('categoryList');
        Route::post('create', [CategoryController::class, 'create'])->name('categoryCreate');
        Route::get('delete/{id}', [CategoryController::class, 'delete'])->name('categoryDelete');
        Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('categoryEdit');
        Route::post('update', [CategoryController::class, 'update'])->name('categoryUpdate');
    });

    Route::prefix('product')->group(function () {
        Route::get('list', [ProductController::class, 'list'])->name('productList');
        Route::get('create', [ProductController::class, 'create'])->name('productCreate');
        Route::post('create', [ProductController::class, 'productCreate'])->name('product_create');
        Route::get('delete/{id}', [ProductController::class, 'delete'])->name('productDelete');
        Route::get('details/{id}', [ProductController::class, 'details'])->name('productDetails');
        Route::get('edit/{id}', [ProductController::class, 'edit'])->name('productEdit');
        Route::post('update', [ProductController::class, 'update'])->name('productUpdate');
    });

    Route::prefix('password') ->group(function () {
        Route::get('change', [AuthController::class, 'change'])->name('passwordChange');
        Route::post('change', [AuthController::class, 'changePassword'])->name('changePassword');
    });

    Route::prefix('payment')->group(function(){
        Route::get('page', [PaymentController::class, 'paymentPage'])->name('paymentPage');
        Route::post('create', [PaymentController::class, 'create'])->name('paymentCreate');
        Route::get('delete/{id}', [PaymentController::class, 'delete'])->name('paymentDelete');
        Route::get('edit/{id}', [PaymentController::class, 'edit'])->name('paymentEdit');
        Route::post('update', [PaymentController::class, 'update'])->name('paymentUpdate');
    });

    Route::prefix('profile') ->group(function () {
        Route::get('detail', [ProfileController::class, 'profileDetail'])->name('profileDetail');
        Route::post('update', [ProfileController::class, 'profileUpdate'])->name('profileUpdate');

        Route::get('create/adminAccount', [ProfileController::class, 'createAdminAccount'])->name('createAdminAccount');
        Route::post('create/adminAccount', [ProfileController::class, 'create'])->name('createAdmin');

    });

    Route::prefix('role') ->group(function () {
        Route::get('adminList',[RoleChangeController::class, 'adminList'])->name('adminList');
        Route::get('deleteAdminAccount/{id}',[RoleChangeController::class, 'deleteAdminAccount'])->name('deleteAdminAccount');

        Route::get('userList',[RoleChangeController::class, 'userList'])->name('userList');
        Route::get('deleteUserAccount/{id}',[RoleChangeController::class, 'deleteUserAccount'])->name('deleteUserAccount');

        Route::get('changeAdminRole/{id}',[RoleChangeController::class, 'changeAdminRole'])->name('changeAdminRole');
        Route::get('changeUserRole/{id}',[RoleChangeController::class, 'changeUserRole'])->name('changeUserRole');

    });


});
