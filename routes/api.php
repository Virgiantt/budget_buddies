
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('accounts', AccountController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('budgets', BudgetController::class);
Route::apiResource('users', UserController::class);