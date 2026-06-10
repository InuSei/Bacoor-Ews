<?php

use App\Http\Controllers\Api\V1\FloodEventController;
use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Intake Endpoint for the ESP32 Hardware (Keep this open)
    Route::post('/flood-events', [FloodEventController::class, 'store']);

    // USER ACCOUNT SEARCH & OTP ENDPOINTS
    Route::post('/auth/find-account', [AuthController::class, 'findAccount']);
    Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Auth Endpoint for Mobile Client/System Login
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    // Protected Presentation Layer Endpoints (Keep other protected items here if any)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/flood-events/latest', [FloodEventController::class, 'latest']);
    });
});