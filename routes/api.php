<?php

use App\Http\Controllers\Api\ExcursionsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelsController;
use App\Http\Controllers\Api\PackagesController;
use App\Http\Controllers\Api\UtilsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Hotels Routes
Route::get('/hotels', [HotelsController::class, 'index']);
Route::get('/hotels-top-3',[HotelsController::class,'top_three']);
Route::get('/hotel/{slug}', [HotelsController::class, 'show']);
// Packages Routes
Route::get('/packages', [PackagesController::class, 'index']);
Route::get('/packages-top-5', [PackagesController::class, 'top_five']);

// Excursions Routes
Route::get('/excursions', [ExcursionsController::class, 'index']);
Route::get('/excursions-top-5', [ExcursionsController::class, 'top_five']);

// Utils Routes
Route::post('/upload-gallery', [UtilsController::class, 'uploadImageProperty']);
Route::post('/upload-media/{propertyId}', [UtilsController::class, 'uploadImageByProperty']);
