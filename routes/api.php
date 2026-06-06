<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Build callback endpoint — rate-limited to 30 calls/minute to prevent abuse
Route::post('/build-callback', [App\Http\Controllers\Api\BuildCallbackController::class, 'handle'])
    ->middleware('throttle:30,1')
    ->name('api.build-callback');
