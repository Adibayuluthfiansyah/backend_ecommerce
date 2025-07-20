<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Models\OrderDetails;
use App\Http\Controllers\CategoryController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //Akun
    // Route::controller(UserController::class)->group(function(){
    //     Route::get('/user', 'index');
    //     Route::post('/user/store', 'store');
    //     Route::patch('/user/{id}/update', 'update');
    //     Route::get('/user/{id}','show');
    //     Route::delete('/user/{id}', 'destroy');
    // });

    Route::apiResource('user', UserController::class);
    Route::apiResource('customer', CustomerController::class);
    Route::apiResource('barang', BarangController::class);
    Route::apiResource('stock', StockController::class);
    Route::apiResource('order', OrderController::class);
    Route::apiResource('orderDetail', OrderDetailsController::class);
    Route::apiResource('categories', CategoryController::class);
    // Route::post('/orderDetail/{id_order}/details', [OrderDetailsController::class, 'store']);
    Route::post('/orderDetail/{orderId}', [OrderDetailsController::class, 'store']);
    Route::patch('/barang/{id}/kurangi-stok', [BarangController::class, 'kurangiStok']);
    Route::get('categories-active', [CategoryController::class, 'getActiveCategories']);
    Route::patch('/order/{id}/updateTotal', [OrderController::class, 'updateTotal']);
    Route::get('/orderDetail/{orderId}', [OrderDetailsController::class, 'getDetailsByOrderId']);
    Route::get('/orderDetail/{order_id}', [OrderDetailsController::class, 'show']);
    Route::get('/dashboard-counts', function () {
        return response()->json([
            'users' => \App\Models\User::count(),
            'customers' => \App\Models\Customer::count(),
            'barangs' => \App\Models\Barang::count(),
            'orders' => \App\Models\Order::count(),
            'total_pendapatan' => \App\Models\Order::sum('total'),
        ]);
    });
});
