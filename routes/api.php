<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
});

Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

Route::prefix('cart')->middleware('auth:api')->group(function () {
    Route::get('/', [CartItemController::class, 'index']);
    Route::post('add', [CartItemController::class, 'store']);
    Route::delete('remove/{cartItem}', [CartItemController::class, 'destroy']);
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
