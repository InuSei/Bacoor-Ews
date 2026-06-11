<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ResidentAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes Configuration Ledger
|--------------------------------------------------------------------------
*/

// 1. ROOT SECURE INTEGRITY GATEWAY
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'resident') {
            return redirect()->route('resident.map-dashboard');
        }
        return redirect()->route('dashboard'); 
    }
    return redirect()->route('login');
});

// 2. CORE SYSTEM AUTHENTICATION PATHS
Route::get('/login', [DashboardController::class, 'showLoginForm'])->name('login');

// 🌟 FIX: Pointed the login submission to the unified Auth Controller
Route::post('/login', [ResidentAuthController::class, 'processLogin'])->name('login.post');

Route::any('/logout', [DashboardController::class, 'logout'])->name('logout');
Route::get('/resident/logout', [DashboardController::class, 'logout'])->name('resident.logout');

// 3. HIGH-FIDELITY RESIDENT PUBLIC CLIENT ROUTE MAPPING BLOCK
Route::prefix('resident')->name('resident.')->group(function () {
    
    Route::get('/find-account', [ResidentAuthController::class, 'showFindAccount'])->name('find-account');
    Route::post('/find-account', [ResidentAuthController::class, 'processFindAccount'])->name('find-account.post');
    
    Route::get('/find-account-email', [ResidentAuthController::class, 'showFindAccountEmail'])->name('find-account-email');
    Route::post('/find-account-email', [ResidentAuthController::class, 'processFindAccountEmail'])->name('find-account-email.post');

    Route::get('/verify-otp', [ResidentAuthController::class, 'showVerifyOtp'])->name('verify-otp');
    Route::post('/verify-otp', [ResidentAuthController::class, 'processVerifyOtp'])->name('verify-otp.post');

    Route::get('/register', [ResidentAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ResidentAuthController::class, 'processRegister'])->name('register.post');

    Route::get('/forgot-password', [ResidentAuthController::class, 'showFindAccount'])->name('forgot-password');
    Route::get('/reset-password', [ResidentAuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [ResidentAuthController::class, 'processResetPassword'])->name('reset-password.post');

    Route::get('/map-dashboard', [ResidentAuthController::class, 'showUserMap'])->name('map-dashboard');
});

// 4. ADMIN DESK WORKSPACE PLATFORM
Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');