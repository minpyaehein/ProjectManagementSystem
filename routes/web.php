<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectFinancialItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public and Guest Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('homePage')
        : redirect()->route('login');
})->name('landing');

Route::middleware('guest')->group(function (): void {
    Route::get(
        '/login',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/login',
        [AuthController::class, 'login']
    )->name('login-form');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/home',
        [HomeController::class, 'index']
    )->name('homePage');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/projects/{project}/documents/{document}/download',
        [ProjectDocumentController::class, 'download']
    )->middleware('permission:projects.view')->name('projects.documents.download');

    Route::delete(
        '/projects/{project}/documents/{document}',
        [ProjectDocumentController::class, 'destroy']
    )->middleware('permission:projects.edit')->name('projects.documents.destroy');

    Route::get(
        '/projects-export',
        [ProjectController::class, 'export']
    )
        ->middleware('permission:projects.view')
        ->name('projects.export');

    Route::get(
        '/projects',
        [ProjectController::class, 'index']
    )
        ->middleware('permission:projects.view')
        ->name('projectsPage');

    Route::get(
        '/project-create',
        [ProjectController::class, 'create']
    )
        ->middleware('permission:projects.create')
        ->name('project-create');

    Route::post(
        '/projects',
        [ProjectController::class, 'store']
    )
        ->middleware('permission:projects.create')
        ->name('projects.store');

    Route::get(
        '/projects/{project}/documents/{document}/download',
        [ProjectDocumentController::class, 'download']
    )
        ->middleware('permission:projects.view')
        ->name('projects.documents.download');

    Route::delete(
        '/projects/{project}/documents/{document}',
        [ProjectDocumentController::class, 'destroy']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.documents.destroy');

    Route::get(
        '/projects/{project}/edit',
        [ProjectController::class, 'edit']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.edit');

    Route::put(
        '/projects/{project}',
        [ProjectController::class, 'update']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.update');

    Route::patch(
        '/projects/{project}/toggle-active',
        [ProjectController::class, 'toggleActive']
    )
        ->middleware('permission:projects.deactivate')
        ->name('projects.toggle-active');

    Route::patch(
        '/projects/{project}/deactivate',
        [ProjectController::class, 'deactivate']
    )
        ->middleware('permission:projects.deactivate')
        ->name('projects.deactivate');

    /*
    |--------------------------------------------------------------------------
    | Project Costs
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/projects/{project}/costs',
        [ProjectFinancialItemController::class, 'storeCost']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.costs.store');

    Route::put(
        '/projects/{project}/costs/{cost}',
        [ProjectFinancialItemController::class, 'updateCost']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.costs.update');

    Route::delete(
        '/projects/{project}/costs/{cost}',
        [ProjectFinancialItemController::class, 'destroyCost']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.costs.destroy');

    /*
    |--------------------------------------------------------------------------
    | Project Revenues
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/projects/{project}/revenues',
        [ProjectFinancialItemController::class, 'storeRevenue']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.revenues.store');

    Route::put(
        '/projects/{project}/revenues/{revenue}',
        [ProjectFinancialItemController::class, 'updateRevenue']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.revenues.update');

    Route::delete(
        '/projects/{project}/revenues/{revenue}',
        [ProjectFinancialItemController::class, 'destroyRevenue']
    )
        ->middleware('permission:projects.edit')
        ->name('projects.revenues.destroy');

    /*
     * Keep the general Project detail route after all specific
     * Project action routes.
     */
    Route::get(
        '/projects/{project}',
        [ProjectController::class, 'show']
    )
        ->middleware('permission:projects.view')
        ->name('projects.show');

    /*
    |--------------------------------------------------------------------------
    | Customers
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/customers',
        [CustomerController::class, 'index']
    )
        ->middleware('permission:customers.view')
        ->name('customersPage');

    Route::get(
        '/customer-create',
        [CustomerController::class, 'create']
    )
        ->middleware('permission:customers.create')
        ->name('customers.create');

    Route::post(
        '/customers',
        [CustomerController::class, 'store']
    )
        ->middleware('permission:customers.create')
        ->name('customers.store');

    Route::get(
        '/customers/{customer}/edit',
        [CustomerController::class, 'edit']
    )
        ->middleware('permission:customers.edit')
        ->name('customers.edit');

    Route::put(
        '/customers/{customer}',
        [CustomerController::class, 'update']
    )
        ->middleware('permission:customers.edit')
        ->name('customers.update');

    Route::patch(
        '/customers/{customer}/deactivate',
        [CustomerController::class, 'deactivate']
    )
        ->middleware('permission:customers.deactivate')
        ->name('customers.deactivate');

    Route::patch(
        '/customers/{customer}/activate',
        [CustomerController::class, 'activate']
    )
        ->middleware('permission:customers.deactivate')
        ->name('customers.activate');

    /*
     * Keep the general Customer detail route after specific actions.
     */
    Route::get(
        '/customers/{customer}',
        [CustomerController::class, 'show']
    )
        ->middleware('permission:customers.view')
        ->name('customers.show');

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/suppliers',
        [SupplierController::class, 'index']
    )
        ->middleware('permission:suppliers.view')
        ->name('suppliersPage');

    Route::get(
        '/supplier-create',
        [SupplierController::class, 'create']
    )
        ->middleware('permission:suppliers.create')
        ->name('suppliers.create');

    Route::post(
        '/suppliers',
        [SupplierController::class, 'store']
    )
        ->middleware('permission:suppliers.create')
        ->name('suppliers.store');

    Route::get(
        '/suppliers/{supplier}/edit',
        [SupplierController::class, 'edit']
    )
        ->middleware('permission:suppliers.edit')
        ->name('suppliers.edit');

    Route::put(
        '/suppliers/{supplier}',
        [SupplierController::class, 'update']
    )
        ->middleware('permission:suppliers.edit')
        ->name('suppliers.update');

    Route::patch(
        '/suppliers/{supplier}/deactivate',
        [SupplierController::class, 'deactivate']
    )
        ->middleware('permission:suppliers.deactivate')
        ->name('suppliers.deactivate');

    Route::patch(
        '/suppliers/{supplier}/activate',
        [SupplierController::class, 'activate']
    )
        ->middleware('permission:suppliers.deactivate')
        ->name('suppliers.activate');

    /*
     * Keep the general Supplier detail route after specific actions.
     */
    Route::get(
        '/suppliers/{supplier}',
        [SupplierController::class, 'show']
    )
        ->middleware('permission:suppliers.view')
        ->name('suppliers.show');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/users',
        [UserController::class, 'index']
    )
        ->middleware('permission:users.view')
        ->name('usersPage');

    Route::get(
        '/user-create',
        [UserController::class, 'create']
    )
        ->middleware('permission:users.create')
        ->name('users.create');

    Route::post(
        '/users',
        [UserController::class, 'store']
    )
        ->middleware('permission:users.create')
        ->name('users.store');

    Route::get(
        '/users/{user}/edit',
        [UserController::class, 'edit']
    )
        ->middleware('permission:users.edit')
        ->name('users.edit');

    Route::put(
        '/users/{user}',
        [UserController::class, 'update']
    )
        ->middleware('permission:users.edit')
        ->name('users.update');

    Route::patch(
        '/users/{user}/reset-password',
        [UserController::class, 'resetPassword']
    )
        ->middleware('permission:users.edit')
        ->name('users.reset-password');

    Route::patch(
        '/users/{user}/deactivate',
        [UserController::class, 'deactivate']
    )
        ->middleware('permission:users.deactivate')
        ->name('users.deactivate');

    Route::patch(
        '/users/{user}/activate',
        [UserController::class, 'activate']
    )
        ->middleware('permission:users.deactivate')
        ->name('users.activate');

    /*
     * Keep the general User detail route after specific actions.
     */
    Route::get(
        '/users/{user}',
        [UserController::class, 'show']
    )
        ->middleware('permission:users.view')
        ->name('users.show');

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/roles',
        [RoleController::class, 'index']
    )
        ->middleware('permission:roles.view')
        ->name('rolesPage');

    Route::get(
        '/role-create',
        [RoleController::class, 'create']
    )
        ->middleware('permission:roles.create')
        ->name('roles.create');

    Route::post(
        '/roles',
        [RoleController::class, 'store']
    )
        ->middleware('permission:roles.create')
        ->name('roles.store');

    Route::get(
        '/roles/{role}/edit',
        [RoleController::class, 'edit']
    )
        ->middleware('permission:roles.edit')
        ->name('roles.edit');

    Route::put(
        '/roles/{role}',
        [RoleController::class, 'update']
    )
        ->middleware('permission:roles.edit')
        ->name('roles.update');

    Route::patch(
        '/roles/{role}/deactivate',
        [RoleController::class, 'deactivate']
    )
        ->middleware('permission:roles.deactivate')
        ->name('roles.deactivate');

    Route::patch(
        '/roles/{role}/activate',
        [RoleController::class, 'activate']
    )
        ->middleware('permission:roles.deactivate')
        ->name('roles.activate');

    /*
     * Keep the general Role detail route after specific actions.
     */
    Route::get(
        '/roles/{role}',
        [RoleController::class, 'show']
    )
        ->middleware('permission:roles.view')
        ->name('roles.show');

    /*
    |--------------------------------------------------------------------------
    | Activity Logs
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/activity',
        fn() => view('activity-logs')
    )
        ->middleware('permission:logs.view')
        ->name('activityPage');

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/logout',
        [AuthController::class, 'logout']
    )->name('logout');
});
