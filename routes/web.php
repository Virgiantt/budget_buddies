
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Transactions
Route::resource('transactions', \App\Http\Controllers\TransactionController::class)
    ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

// Categories
Route::resource('categories', \App\Http\Controllers\CategoryController::class);

// Accounts
use App\Http\Controllers\AccountPageController;

Route::resource('accounts', AccountPageController::class)
    ->except(['show']);

// Budgets
Route::resource('budgets', \App\Http\Controllers\BudgetController::class);

// Authentication
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');
