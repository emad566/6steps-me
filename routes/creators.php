<?php

use App\Http\Controllers\Creator\CreatorAuthController;
use App\Http\Controllers\Creator\CreatorController;
use Illuminate\Support\Facades\Route;


// Start:: Brand ================================================================= //
Route::put('updateProfile/{id}', [CreatorAuthController::class, 'updateProfile']);
// End:: Brand ================================================================= //
