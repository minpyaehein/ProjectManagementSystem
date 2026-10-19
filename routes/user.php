<?php

use App\Http\Controllers\User\UserDashboardController;
use Illuminate\Support\Facades\Route;

//Customer
Route::group(['prefix' => 'customer', 'middleware' => ['auth', 'user']], function () {
    Route::get('/home', [UserDashboardController::class, 'index'])->name('userDashboard');
});
