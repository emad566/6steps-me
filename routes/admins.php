<?php

use App\Http\Controllers\Admin\CatController;
use App\Http\Controllers\Admin\CityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Start::Category ===============================================//
Route::resource('cats', CatController::class);
Route::put('cats/{id}/toggleActive', [CatController::class, 'toggleActive']);
// En::Category ===============================================//

// Start::City ===============================================//
Route::resource('cities', CityController::class);
Route::put('cities/{id}/toggleActive', [CityController::class, 'toggleActive']);
// En::City ===============================================//
