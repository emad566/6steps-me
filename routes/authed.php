<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CatController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\Brand\BrandAuthController;
use App\Http\Controllers\Brand\BrandController;
use App\Http\Controllers\Brand\CampaignController;
use App\Http\Controllers\Brand\CampaignRequestController;
use App\Http\Controllers\Brand\RequestVideoController;
use App\Http\Controllers\Creator\CreatorAuthController;
use App\Http\Controllers\Creator\CreatorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Start::Files ===================================================== //
Route::post('uploadFile', [FileController::class, 'uploadFile']);
Route::Delete('deleteFile', [FileController::class, 'deleteFile']);
// Start::Files ===================================================== //

// Start::Brand ===================================================== //
Route::resource('brands', BrandController::class)->only(['show', 'index']);
// Start::Brand ===================================================== //

// Start::Creator ===================================================== //
Route::resource('creators', CreatorController::class)->only(['show', 'index']);
// Start::Creator ===================================================== //

// Start::Category ===================================================== //
Route::get('cats', [CatController::class, 'index']);
Route::get('cities', [CityController::class, 'index']);
// Start::Category ===================================================== //

// Start::City ===================================================== //
Route::get('cities', [CityController::class, 'index']);
// Start::City ===================================================== //

// Start::Campaign ================================================================= //
Route::get('campaigns', [CampaignController::class,  'index']);
// End::Campaign ================================================================= //

// Start::CampaignRequest ================================================================= //
Route::resource('requests', CampaignRequestController::class)->only(['index', 'show']);
// End::CampaignRequest ================================================================= //

// Start::RequestVideo ================================================================= //
Route::resource('requestvideos', RequestVideoController::class)->only(['index', 'show']);
// End::RequestVideo ================================================================= //

