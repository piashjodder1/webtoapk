<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/build-callback', [App\Http\Controllers\Api\BuildCallbackController::class, 'handle'])->name('api.build-callback');
