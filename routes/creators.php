<?php

use App\Http\Controllers\Brand\CampaignRequestController;
use App\Http\Controllers\Creator\CreatorAuthController;
use App\Http\Controllers\Creator\CreatorController;
use Illuminate\Support\Facades\Route;


// Start:: Creator ================================================================= //
Route::put('updateProfile/{id}', [CreatorAuthController::class, 'updateProfile']);
Route::resource('creators', CreatorController::class)->except(['destroy', 'store', 'create']);
Route::put('creators/{id}/samplevideos', [CreatorController::class, 'samplevideos'])
    ->where(['id' => '[0-9]+']); 
// End::Creator ===================================================== //

// Start::CampaignRequest ================================================================= //
Route::resource('requests', CampaignRequestController::class)->only(['create','store','edit','update']);
// End::CampaignRequest ================================================================= //