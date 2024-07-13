<?php

use App\Http\Controllers\Creator\CreatorAuthController;
use Illuminate\Support\Facades\Route;

Route::put('updateProfile/{id}', [CreatorAuthController::class, 'updateProfile']);
