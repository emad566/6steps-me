<?php

use App\Http\Controllers\Brand\BrandAuthController;
use App\Http\Controllers\Brand\BrandController;
use Illuminate\Support\Facades\Route;


// Start:: Brand ================================================================= //
Route::put('updateProfile/{id}', [BrandAuthController::class, 'updateProfile']);
// End:: Brand ================================================================= //
