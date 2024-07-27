<?php

use App\Http\Controllers\Admin\AdminAuthController; 
use App\Http\Controllers\Brand\BrandAuthController;
use App\Http\Controllers\Creator\CreatorAuthController; 
use Illuminate\Support\Facades\Route;

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



Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [AdminAuthController::class, 'login']);
});

Route::group(['prefix' => 'creator'], function () {
    Route::post('loginRegisterResendOtp', [CreatorAuthController::class, 'loginRegisterResendOtp']);
    Route::post('otpVerify', [CreatorAuthController::class, 'otpVerify']);
});

Route::group(['prefix' => 'brand'], function () {
    Route::post('loginRegisterResendOtp', [BrandAuthController::class, 'loginRegisterResendOtp']);
    Route::post('otpVerify', [BrandAuthController::class, 'otpVerify']);
});
