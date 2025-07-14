<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalConditionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/cameras', [CameraController::class, 'index']);
Route::get('/cameras/{camera}', [CameraController::class, 'show']);
Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

// Rute yang memerlukan login (bisa untuk customer & admin)
Route::middleware(['auth:api', 'role:customer'])->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::post('/my-bookings/{booking}/complete-data', [BookingController::class, 'completeData']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/cameras', [CameraController::class, 'store']);
    Route::delete('/cameras/{camera}', [CameraController::class, 'destroy']);
    Route::match(['PUT', 'POST'], '/cameras/{camera}', [CameraController::class, 'update']);
    // Admin menyetujui pesanan
    Route::patch('/rentals/{rental}/approve', [RentalController::class, 'approve']);
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    Route::get('/bookings', [BookingController::class, 'index']);

    // Rute yang dikelola oleh BookingController
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    
    // Rute yang sekarang dikelola oleh controller payment dan kondisi
    Route::post('/bookings/{booking}/payments', [PaymentController::class, 'store']);
    Route::post('/bookings/{booking}/conditions', [RentalConditionController::class, 'store']);
});