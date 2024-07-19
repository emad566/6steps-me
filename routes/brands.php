<?php

use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\BrandController;
use Illuminate\Support\Facades\Route;

 
// Start::Brand ================================================================= //
Route::put('updateProfile/{id}', [BrandController::class, 'update']);
Route::resource('brands', BrandController::class)->except(['destroy', 'store', 'create']);
// End::Brand ===================================================== //


// Start::Campaign ================================================================= //
Route::resource('campaigns', CampaignController::class)->except(['destroy']);
Route::put('campaigns/{id}/updateStatus', [CampaignController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::Campaign ================================================================= //
