<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;

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

// User Routes
Route::group(['prefix' => 'users'], function () {
    Route::post('/create', [UserController::class, 'CreateUser']);
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {

    // User Routes
    Route::post('/users/logout', [UserController::class, 'logout']);

    //Project Routes
    Route::group(['prefix' => 'projects'], function () {
        Route::get('/', [ProjectController::class, 'AllProjects']);
        Route::post('/create', [ProjectController::class, 'CreateProject']);
        Route::post('/search', [ProjectController::class, 'SearchProject']);
        Route::post('/delete', [ProjectController::class, 'DeleteProject']);
    });

    // Donation Routes
    Route::group(['prefix' => 'donations'], function () {
        Route::get('/', [DonationController::class, 'AllDonations']);
        Route::post('/create', [DonationController::class, 'CreateDonation']);
        Route::get('/search', [DonationController::class, 'SearchDonation']);
    });
});
