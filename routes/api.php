<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelsController;
use App\Http\Controllers\Api\PackagesController;
use App\Http\Controllers\Api\UtilsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/hotels', [HotelsController::class, 'index']);
Route::get('/hotels-top-3',[HotelsController::class,'top_three']);
Route::get('/hotel/{slug}', [HotelsController::class, 'show']);

Route::get('/packages', [PackagesController::class, 'index']);
Route::get('/packages-top-5', [PackagesController::class, 'top_five']);


Route::post('/upload-gallery', [UtilsController::class, 'uploadImageProperty']);
Route::post('/upload-media/{propertyId}', [UtilsController::class, 'uploadImageByProperty']);
