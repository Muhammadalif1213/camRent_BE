<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CameraController; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/cameras', [CameraController::class, 'index']);
Route::get('/cameras/{camera}', [CameraController::class, 'show']);

Route::middleware(['auth:api', 'role:admin'])->group(function () {

    Route::post('/cameras', [CameraController::class, 'store']);
    Route::delete('/cameras/{camera}', [CameraController::class, 'destroy']);
    Route::put('/cameras/{camera}', [CameraController::class, 'update']);
});