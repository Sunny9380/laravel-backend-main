<?php

use App\Http\Controllers\v1\auth\AuthController;
use App\Http\Controllers\v1\auth\CodeCheckController;
use App\Http\Controllers\v1\auth\ForgotPasswordController;
use App\Http\Controllers\v1\auth\ResetPasswordController;
use App\Http\Controllers\v1\global\VerificationController;
use Illuminate\Support\Facades\Route;

Route::controller(VerificationController::class)->group(function () {
    Route::get('email/verify/{id}', 'verify')->name('verification.verify');
    Route::get('email/resend', 'verify')->name('verification.resend');
});

Route::get('/unauthorize', function () {
    return response()->json('Unauthorized');
})->name('unauthorize');

Route::post('password/email',  ForgotPasswordController::class);
Route::post('password/code/check', CodeCheckController::class);
Route::post('password/reset', ResetPasswordController::class);

Route::controller(AuthController::class)->group(function () {
    Route::post('authenticate', 'authenticate')->name('authenticate');
});
