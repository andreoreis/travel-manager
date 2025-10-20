<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelRequestController;
use App\Http\Middleware\AdminMiddleware;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('auth/logout',  [AuthController::class, 'logout']);
    Route::post('auth/refresh', [AuthController::class, 'refresh']);
    Route::get('auth/me',       [AuthController::class, 'me']);

    Route::apiResource('travel-requests', TravelRequestController::class);

    Route::patch('travel-requests/{travel_request}/status', [TravelRequestController::class, 'changeStatus'])->middleware([AdminMiddleware::class]);
    Route::patch('travel-requests/{travel_request}/cancel', [TravelRequestController::class, 'cancel'])->middleware([AdminMiddleware::class]);
});
