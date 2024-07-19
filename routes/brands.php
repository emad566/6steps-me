<?php

use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\BrandAuthController;
use Illuminate\Support\Facades\Route;


// Start:: Brand ================================================================= //
Route::put('updateProfile/{id}', [BrandAuthController::class, 'updateProfile']);
// End:: Brand ================================================================= //

// Start::Campaign ================================================================= //
Route::resource('campaigns', CampaignController::class)->except(['destroy']);
Route::put('campaigns/{id}/updateStatus', [CampaignController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::Campaign ================================================================= //
