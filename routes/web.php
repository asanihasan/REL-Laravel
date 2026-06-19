<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;

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
    
    // Historical Data Endpoint
    Route::get('/pumps/{id}/history', [PumpController::class, 'history'])->name('pumps.history');

    // Maps Placeholder
    Route::get('/maps', [PumpController::class, 'maps'])->name('pumps.maps');

    // ==========================================
    // User Management
    // ==========================================
    Route::get('/manage/users', [UserManagementController::class, 'index'])->name('manage.user');
    
    // User CRUD
    Route::post('/users', [UserManagementController::class, 'storeUser']);
    Route::put('/users/{id}', [UserManagementController::class, 'updateUser']);
    Route::delete('/users/{id}', [UserManagementController::class, 'destroyUser']);

    // User Group CRUD
    Route::post('/user-groups', [UserManagementController::class, 'storeGroup']);
    Route::put('/user-groups/{id}', [UserManagementController::class, 'updateGroup']);
    Route::delete('/user-groups/{id}', [UserManagementController::class, 'destroyGroup']);

    // Alerts Placeholder
    Route::view('/manage/alerts', 'manage.alert')->name('manage.alert');

    Route::post('/users/{id}/telegram-link', [UserManagementController::class, 'generateUserTelegramLink']);
});

Route::post('/telegram/webhook', [App\Http\Controllers\TelegramController::class, 'handleWebhook']);