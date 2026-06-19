<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('pumps', PumpController::class);
    Route::get('/pumps/{id}/data', [PumpController::class, 'data'])->name('pumps.data');
    Route::post('/pumps/{id}/control', [PumpController::class, 'control'])->name('pumps.control');
    Route::get('/pumps/{id}/monitor', [PumpController::class, 'monitor'])->name('pumps.monitor');
    
    // NEW: Historical Data Endpoint
    Route::get('/pumps/{id}/history', [PumpController::class, 'history'])->name('pumps.history');

    // Maps Placeholder
    Route::get('/maps', [App\Http\Controllers\PumpController::class, 'maps'])->name('pumps.maps');

    // User Management Placeholder
    Route::view('/manage/users', 'manage.user')->name('manage.user');

    // Alerts Placeholder
    Route::view('/manage/alerts', 'manage.alert')->name('manage.alert');
});
