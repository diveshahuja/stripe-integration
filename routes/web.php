<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::prefix('orders')->name('orders.')->group(function () {
    Route::post('/', [OrderController::class, 'store'])->name('store');
    Route::any('verify-payment/{driver}/{transaction_id}', [OrderController::class, 'verifyOrder'])->name('verify-order');
});
