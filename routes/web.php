<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectFinancialItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/home', [HomeController::class, 'index'])
        ->name('homePage');

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::put(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::put(
        '/profile/password',
        [ProfileController::class, 'updatePassword']
    )->name('profile.password.update');

    Route::middleware('auth')->group(function (): void {
        Route::get(
            '/profile',
            [ProfileController::class, 'edit']
        )->name('profile.edit');

        Route::put(
            '/profile',
            [ProfileController::class, 'update']
        )->name('profile.update');

        Route::put(
            '/profile/password',
            [ProfileController::class, 'updatePassword']
        )->name('profile.password.update');
    });

    // Projects

    Route::get('/projects-export', [ProjectController::class, 'export'])
        ->middleware('permission:projects.view')
        ->name('projects.export');
    Route::get('/projects', [ProjectController::class, 'index'])
        ->middleware('permission:projects.view')->name('projectsPage');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->middleware('permission:projects.view')->name('projects.show');
    Route::get('/project-create', [ProjectController::class, 'create'])
        ->middleware('permission:projects.create')->name('project-create');
    Route::post('/projects', [ProjectController::class, 'store'])
        ->middleware('permission:projects.create')->name('projects.store');
    Route::patch(
        '/projects/{project}/toggle-active',
        [ProjectController::class, 'toggleActive']
    )
        ->middleware([
            'auth',
            'permission:projects.deactivate',
        ])
        ->name('projects.toggle-active');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])
        ->middleware('permission:projects.edit')->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])
        ->middleware('permission:projects.edit')->name('projects.update');
    Route::patch('/projects/{project}/deactivate', [ProjectController::class, 'deactivate'])
        ->middleware('permission:projects.deactivate')->name('projects.deactivate');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])
        ->middleware('permission:customers.view')->name('customersPage');
    Route::get('/customer-create', [CustomerController::class, 'create'])
        ->middleware('permission:customers.create')->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('permission:customers.create')->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])
        ->middleware('permission:customers.edit')->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])
        ->middleware('permission:customers.edit')->name('customers.update');
    Route::patch('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])
        ->middleware('permission:customers.deactivate')->name('customers.deactivate');
    Route::get(
        '/customers/{customer}',
        [CustomerController::class, 'show']
    )
        ->middleware('permission:customers.view')
        ->name('customers.show');

    Route::patch(
        '/customers/{customer}/activate',
        [
            CustomerController::class,
            'activate',
        ]
    )
        ->middleware([
            'auth',
            'permission:customers.deactivate',
        ])
        ->name('customers.activate');

    // Suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])
        ->middleware('permission:suppliers.view')->name('suppliersPage');
    Route::get('/supplier-create', [SupplierController::class, 'create'])
        ->middleware('permission:suppliers.create')->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])
        ->middleware('permission:suppliers.create')->name('suppliers.store');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
        ->middleware('permission:suppliers.edit')->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
        ->middleware('permission:suppliers.edit')->name('suppliers.update');
    Route::patch('/suppliers/{supplier}/deactivate', [SupplierController::class, 'deactivate'])
        ->middleware('permission:suppliers.deactivate')->name('suppliers.deactivate');
    Route::get(
        '/suppliers/{supplier}',
        [SupplierController::class, 'show']
    )
        ->middleware('permission:suppliers.view')
        ->name('suppliers.show');

    Route::patch(
        '/suppliers/{supplier}/activate',
        [SupplierController::class, 'activate']
    )
        ->middleware([
            'auth',
            'permission:suppliers.deactivate',
        ])
        ->name('suppliers.activate');

    // Users
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')->name('usersPage');
    Route::get('/user-create', [UserController::class, 'create'])
        ->middleware('permission:users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.edit')->name('users.update');
    Route::patch('/users/{user}/deactivate', [UserController::class, 'deactivate'])
        ->middleware('permission:users.deactivate')->name('users.deactivate');

    Route::patch(
        '/users/{user}/activate',
        [UserController::class, 'activate']
    )
        ->middleware('permission:users.deactivate')
        ->name('users.activate');

    Route::get(
        '/users/{user}',
        [UserController::class, 'show']
    )
        ->middleware('permission:users.view')
        ->name('users.show');
    Route::patch(
        '/users/{user}/reset-password',
        [
            UserController::class,
            'resetPassword',
        ]
    )
        ->middleware([
            'auth',
            'permission:users.edit',
        ])
        ->name('users.reset-password');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('permission:roles.view')->name('rolesPage');
    Route::get('/role-create', [RoleController::class, 'create'])
        ->middleware('permission:roles.create')->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.create')->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->middleware('permission:roles.view')->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:roles.edit')->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.edit')->name('roles.update');
    Route::patch('/roles/{role}/deactivate', [RoleController::class, 'deactivate'])
        ->middleware('permission:roles.deactivate')->name('roles.deactivate');

    Route::get('/activity', fn() => view('activity-logs'))
        ->middleware('permission:logs.view')->name('activityPage');

});

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login-form');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'permission:projects.edit'])->group(function (): void {
    Route::post('/projects/{project}/costs', [ProjectFinancialItemController::class, 'storeCost'])
        ->name('projects.costs.store');
    Route::put('/projects/{project}/costs/{cost}', [ProjectFinancialItemController::class, 'updateCost'])
        ->name('projects.costs.update');
    Route::delete('/projects/{project}/costs/{cost}', [ProjectFinancialItemController::class, 'destroyCost'])
        ->name('projects.costs.destroy');

    Route::post('/projects/{project}/revenues', [ProjectFinancialItemController::class, 'storeRevenue'])
        ->name('projects.revenues.store');
    Route::put('/projects/{project}/revenues/{revenue}', [ProjectFinancialItemController::class, 'updateRevenue'])
        ->name('projects.revenues.update');
    Route::delete('/projects/{project}/revenues/{revenue}', [ProjectFinancialItemController::class, 'destroyRevenue'])
        ->name('projects.revenues.destroy');
});
