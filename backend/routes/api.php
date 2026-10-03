<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('throttle:10,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/2fa', [AuthController::class, 'verifyTwoFactor']);
});

Route::middleware(['jwt'])->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
});
