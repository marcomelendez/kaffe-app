<?php

use App\Models\DominioProducto;
use App\Models\Photo;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\HotelsController;
use App\Http\Controllers\Api\UtilsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/hotels', [HotelsController::class, 'index']);
Route::get('/hotels-top-3',[HotelsController::class,'top_three']);
Route::get('/hotel/{slug}', [HotelsController::class, 'show']);


Route::post('/upload-gallery', [UtilsController::class, 'uploadImageProperty']);
Route::post('/upload-media/{propertyId}', [UtilsController::class, 'uploadImageByProperty']);
