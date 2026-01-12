<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\AuthController;

// 1. Redirect Home Page to Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Protected App Routes (Only accessible after login)
Route::middleware('auth')->group(function () {
    Route::resource('pumps', PumpController::class);
    
    Route::post('/pumps/{id}/control', [PumpController::class, 'control'])->name('pumps.control');

    Route::get('/pumps/{id}/data', [PumpController::class, 'data'])->name('pumps.data');
});