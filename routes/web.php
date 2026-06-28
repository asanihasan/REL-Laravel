<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PumpController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AlertController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::put('/pumps/{pump}', [PumpController::class, 'update'])->middleware('permission:data_manager');
    Route::delete('/pumps/{pump}', [PumpController::class, 'destroy'])->middleware('permission:data_manager');
    
    Route::get('/pumps', [PumpController::class, 'index']);
    Route::get('/pumps/{pump}', [PumpController::class, 'show'])->middleware('permission:view');
    Route::get('/pumps/{id}/data', [PumpController::class, 'data'])->middleware('permission:view')->name('pumps.data');
    Route::post('/pumps/{id}/control', [PumpController::class, 'control'])->middleware('permission:control')->name('pumps.control');
    Route::get('/pumps/{id}/monitor', [PumpController::class, 'monitor'])->middleware('permission:view')->name('pumps.monitor');
    
    // Historical Data Endpoint
    Route::get('/pumps/{id}/history', [PumpController::class, 'history'])->name('pumps.history')->middleware('permission:view');

    // Maps Placeholder
    Route::get('/maps', [PumpController::class, 'maps'])->name('pumps.maps');

    // User Management
    Route::get('/manage/users', [UserManagementController::class, 'index'])->middleware('permission:administrator')->name('manage.user');
    
    // User CRUD
    Route::post('/users', [UserManagementController::class, 'storeUser'])->middleware('permission:administrator');
    Route::put('/users/{id}', [UserManagementController::class, 'updateUser'])->middleware('permission:administrator');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroyUser'])->middleware('permission:administrator');

    Route::put('/users/{id}/credentials', [UserManagementController::class, 'updateCredentials'])->name('users.credentials');

    // User Group CRUD
    Route::post('/user-groups', [UserManagementController::class, 'storeGroup'])->middleware('permission:administrator');
    Route::put('/user-groups/{id}', [UserManagementController::class, 'updateGroup'])->middleware('permission:administrator');
    Route::delete('/user-groups/{id}', [UserManagementController::class, 'destroyGroup'])->middleware('permission:administrator');

    // Alert Pages
    Route::get('/manage/alerts', [AlertController::class, 'index'])->name('manage.alert')->middleware('permission:administrator');
    Route::get('/manage/alerts/data', [AlertController::class, 'data'])->name('manage.alert.data')->middleware('permission:administrator');

    Route::post('/users/{id}/telegram-link', [UserManagementController::class, 'generateUserTelegramLink']);
});

Route::post('/telegram/webhook', [App\Http\Controllers\TelegramController::class, 'handleWebhook']);
