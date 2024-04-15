<?php

use App\Http\Controllers\Api\FileApiController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

Route::get('/info', [ServerController::class, 'index']);
Route::apiResource('/file', FileApiController::class);
Route::post('/bucket', [ServerController::class, 'createBucket']);
Route::post('/bucket/check', [ServerController::class, 'check']);