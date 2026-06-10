<?php

use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');
});

Route::any('/logout', [AuthController::class, 'logout'])->name('logout');



Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    // UddoktaPay / Automated Gateway Routes
    Route::get('/payment/checkout/{plan}/{gateway}', [\App\Http\Controllers\PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::any('/payment/success/{gateway}', [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::any('/payment/cancel/{gateway}', [\App\Http\Controllers\PaymentController::class, 'cancel'])->name('payment.cancel');
});

// Webhook route must be outside the 'auth' middleware and CSRF protected area 
// since it receives a POST request from an external server.
Route::post('/payment/webhook/{gateway}', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');
