<?php

use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Brand\CampaignRequestController;
use App\Http\Controllers\Brand\RequestVideoController;
use App\Models\CampaignRequest;
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

// Start::CampaignRequest ================================================================= //
Route::put('requests/{id}/updateStatus', [CampaignRequestController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::CampaignRequest ================================================================= //

// Start::RequestVideo ================================================================= //
Route::put('requestvideos/{id}/updateStatus', [RequestVideoController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::RequestVideo ================================================================= //


