<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - v1
|--------------------------------------------------------------------------
| All URIs start with /api/v1 as specified in Question 3(a).
*/

Route::prefix('v1')->group(function () {

    // Login route with throttle middleware (Fix C for Problem M3 / Q1(b) 5 attempts per minute)
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // Authenticated Sanctum Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Question 3(a): Customer lists their orders (10 per page)
        Route::get('/orders', [OrderController::class, 'index']);

        // Question 3(a): Customer views one order (protected by OrderPolicy@view against IDOR)
        Route::get('/orders/{order}', [OrderController::class, 'show']);

        // Question 3(a): Customer books a pickup (returns 201 Created)
        Route::post('/bookings', [OrderController::class, 'storeBooking']);
        Route::post('/orders', [OrderController::class, 'storeBooking']);

        // Question 3(a): Staff updates an order's status (protected by OrderPolicy@updateStatus)
        Route::match(['patch', 'put'], '/orders/{order}/status', [OrderController::class, 'updateStatus']);

        // List services catalog
        Route::get('/services', function () {
            return \App\Http\Resources\ServiceResource::collection(\App\Models\Service::all());
        });
    });
});
