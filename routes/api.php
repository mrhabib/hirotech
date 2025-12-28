<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Api\ReservationController;
/*
|--------------------------------------------------------------------------
| Public routes (no auth required)
|--------------------------------------------------------------------------
*/
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('user/login', [UserAuthController::class, 'login']);
Route::get('events', [EventController::class, 'index']); // Public events list

/*
|--------------------------------------------------------------------------
| Admin-only routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth.api:admin-api')->group(function () {
    Route::apiResource('events', EventController::class);
    Route::get('admin/profile', function () {
        return new \App\Http\Resources\ApiResponseResource(
            auth('admin-api')->user(),
            'Admin profile retrieved successfully',
            \App\Enums\HttpStatusCode::OK
        );
    });
});

/*
|--------------------------------------------------------------------------
| User-only routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth.api:user-api')->group(function () {
    Route::get('events', [EventController::class, 'index']);
    Route::get('user/profile', function () {
        return new \App\Http\Resources\ApiResponseResource(
            auth('user-api')->user(),
            'User profile retrieved successfully',
            \App\Enums\HttpStatusCode::OK
        );
    });
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::patch('reservations/{reservationId}/activate', [ReservationController::class, 'activate']);
    Route::delete('reservations/{reservationId}', [ReservationController::class, 'destroy']);
});
