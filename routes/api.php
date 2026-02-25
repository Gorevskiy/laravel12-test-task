<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);
Route::get('users/{user}/profile', [UserProfileController::class, 'show']);
Route::put('users/{user}/profile', [UserProfileController::class, 'update']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orders', OrderController::class);

Route::post('orders/{order}/items', [OrderItemController::class, 'store']);
Route::delete('orders/{order}/items/{product}', [OrderItemController::class, 'destroy']);
