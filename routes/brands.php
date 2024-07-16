<?php

use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\BrandAuthController;
use Illuminate\Support\Facades\Route;


// Start:: Brand ================================================================= //
Route::put('updateProfile/{id}', [BrandAuthController::class, 'updateProfile']);
// End:: Brand ================================================================= //

// Start::Campaign ================================================================= //
Route::resource('campaigns', CampaignController::class);
Route::put('campaigns/{id}/toggleActive/{state}', [CampaignController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
// End::Campaign ================================================================= //
