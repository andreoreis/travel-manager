<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelRequestController;

//Auth routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login',    [AuthController::class, 'login']);

// Rotas protegidas por auth
Route::middleware('auth:api')->group(function () {
    //Auth routes
    Route::post('auth/logout',  [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me',       [AuthController::class, 'me']);

    //TravelRequests routes
    Route::apiResource('travel-requests', TravelRequestController::class);
    Route::post('travel-requests/{travel_request}/status', [TravelRequestController::class, 'changeStatus']);
});
