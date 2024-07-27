<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CatController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\CampaignRequestController;
use App\Http\Controllers\Brand\RequestVideoController;
use App\Http\Controllers\Creator\CreatorController;
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

// Start::Admin ===================================================== //
Route::resource('admins', AdminController::class);
Route::put('admins/{id}/samplevideos', [AdminController::class, 'samplevideos'])
    ->where(['id' => '[0-9]+']);
Route::put('admins/{id}/toggleActive/{state}', [AdminController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
// End::Admin ===================================================== //


// Start::Creator ===================================================== //
Route::resource('creators', CreatorController::class);
Route::put('creators/{id}/samplevideos', [CreatorController::class, 'samplevideos'])
    ->where(['id' => '[0-9]+']);
Route::put('creators/{id}/toggleActive/{state}', [CreatorController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
// End::Creator ===================================================== //

// Start::Brand ===================================================== //
Route::resource('brands', BrandController::class);
Route::put('brands/{id}/toggleActive/{state}', [BrandController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
// End::Brand ===================================================== //


// Start::Campaign ================================================================= //
Route::resource('campaigns', CampaignController::class);
Route::put('campaigns/{id}/toggleActive/{state}', [CampaignController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
Route::put('campaigns/{id}/updateStatus', [CampaignController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::Campaign ================================================================= //


// Start::CampaignRequest ================================================================= //
Route::resource('requests', CampaignRequestController::class)->only(['edit', 'update', 'destroy']);
Route::put('requests/{id}/toggleActive/{state}', [CampaignRequestController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
Route::put('requests/{id}/updateStatus', [CampaignRequestController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::CampaignRequest ================================================================= //

// Start::RequestVideo ================================================================= //
Route::resource('requestvideos', RequestVideoController::class)->only(['edit', 'update', 'destroy']);
Route::put('requestvideos/{id}/toggleActive/{state}', [RequestVideoController::class, 'toggleActive'])
    ->where(['id' => '[0-9]+', 'state' => '0|1']);
Route::put('requestvideos/{id}/updateStatus', [RequestVideoController::class, 'updateStatus'])
->where(['id' => '[0-9]+']);
// End::RequestVideo ================================================================= //