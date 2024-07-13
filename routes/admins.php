<?php

use App\Http\Controllers\Admin\CatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Start::Category ===============================================//
Route::resource('cats', CatController::class);
Route::put('cats/{id}/toggleActive', [CatController::class, 'toggleActive']);
// En::Category ===============================================//

