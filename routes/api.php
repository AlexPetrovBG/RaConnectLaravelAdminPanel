<?php

use App\Http\Controllers\Auth\ApiLoginController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/login', [ApiLoginController::class, 'login'])->middleware('throttle:60,1');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiLoginController::class, 'logout']);
    Route::get('/me', MeController::class);
    
    // Project routes with permission middleware
    Route::get('/projects', [ProjectController::class, 'index'])->middleware('permission:projects.view');
    Route::post('/projects', [ProjectController::class, 'store'])->middleware('permission:projects.edit');
});


