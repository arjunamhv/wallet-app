<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\ApiAuthMiddleware;


Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => ApiAuthMiddleware::class], function () {
    Route::get('/user', [UserController::class, 'get']);
    Route::patch('/user', [UserController::class, 'update']);
    Route::delete('/logout', [UserController::class, 'logout']);


    Route::post('/topup', [TopupController::class, 'topup']);
    Route::post('/payment', [PaymentController::class, 'payment']);
    Route::post('/transfer', [TransferController::class, 'transfer']);
    Route::get('/transaction', [TransactionController::class, 'transaction']);
});
