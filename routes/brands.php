<?php

use App\Http\Controllers\Brand\BrandAuthController;
use Illuminate\Support\Facades\Route;

Route::put('updateProfile/{id}', [BrandAuthController::class, 'updateProfile']);
