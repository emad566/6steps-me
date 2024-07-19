<?php

use App\Http\Controllers\Admin\CatController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Brand\CampaignController;
use App\Models\AppConstants;
use Illuminate\Support\Facades\Route;

// Start::Category ===============================================//
Route::resource('cats', CatController::class);
Route::put('cats/{id}/toggleActive/{state}', [CatController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
// En::Category ===============================================//

// Start::City ===============================================//
Route::resource('cities', CityController::class);
Route::put('cities/{id}/toggleActive/{state}', [CityController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
// En::City ===============================================//

// Start::Campaign ================================================================= //
Route::resource('campaigns', CampaignController::class);
Route::put('campaigns/{id}/toggleActive/{state}', [CampaignController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
Route::put('campaigns/{id}/updateStatus', [CampaignController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::Campaign ================================================================= //

