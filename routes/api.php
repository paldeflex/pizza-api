<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
});

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
