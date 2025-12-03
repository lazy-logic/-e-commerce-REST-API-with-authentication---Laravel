<?php

/**
 * API routes for the e-commerce backend.
 *
 * These routes are loaded by the RouteServiceProvider and are typically
 * prefixed with /api and protected with Sanctum where authentication is required.
 */

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminController;








Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);





Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    
    Route::get('/cart', [CartController::class, 'view']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove', [CartController::class, 'remove']);

    
    Route::post('/checkout', [CheckoutController::class, 'checkout']);

    
    Route::post('/payment/simulate', [PaymentController::class, 'simulate']);

    
    Route::get('/orders/my', [OrderController::class, 'myOrders']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    
    
    
    

    Route::prefix('admin')->group(function () {
        
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        
        Route::get('/orders', [OrderController::class, 'index']);

        
        Route::post('/restock', [AdminController::class, 'restock']);
    });
});
