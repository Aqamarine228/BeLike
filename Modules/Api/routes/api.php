<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\app\Http\Controllers\LoginController;
use Modules\Api\app\Http\Controllers\RegisterController;
use Modules\Api\Http\Controllers\ApiController;
use Modules\Api\Http\Controllers\EmailVerificationController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::post('/login', [LoginController::class, 'login']);
Route::post('/social-login', [LoginController::class, 'socialLogin']);

Route::post('/register', [RegisterController::class, 'register']);

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('email.verify')->middleware(['signed']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware(['throttle:1,1']);
    Route::post('/email/check-verification', [EmailVerificationController::class, 'checkVerification']);
});
