<?php

use App\Http\Controllers\Api\ChittyController;
use App\Http\Controllers\Api\CoordinatorController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [LoginController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    //! Coordinators
    Route::get('/coordinators', [CoordinatorController::class, 'index']);
    Route::post('/coordinators', [CoordinatorController::class, 'store']);
    Route::get('/coordinators/{id}', [CoordinatorController::class, 'show']);
    Route::put('/coordinators/{id}', [CoordinatorController::class, 'update']);
    Route::delete('/coordinators/{id}', [CoordinatorController::class, 'destroy']);

    //! Customers
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
    Route::get('/due-customers', [CustomerController::class, 'dueCustomers']);
    Route::get('/winners', [CustomerController::class, 'winners']);

    //! Transactions
    Route::get('/transactions/{month}', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/mark-transaction', [TransactionController::class, 'markAsSelected']);
    Route::get('/selected-transactions', [TransactionController::class, 'getSelectedTransactions']);
    Route::get('/customer-transactions/{id}', [TransactionController::class, 'getTransactionByCustomerId']);
    // Route::get('/due-transactions', [TransactionController::class, 'getDueCustomers']);

    //! Home
    Route::get('/home', [HomeController::class, 'index']);

    //! Chitty
    Route::get('/chitty', [ChittyController::class, 'getChitties']);
    Route::post('/chitty', [ChittyController::class, 'createChitty']);
    Route::get('/chitty-count', [ChittyController::class, 'getChittyCount']);
    Route::post('/mark-winner', [ChittyController::class, 'markAsChittyWinner']);

});
