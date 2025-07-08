<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\LoanPayments;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Livewire\LoanManager;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('loans.index')
        : redirect()->route('login');
});

Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [UserController::class, 'register']);
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['auth'])->group(function () {
        Route::get('/loans', LoanManager::class)->name('loans.index');
    });

    Route::get('/loans/{loanId}/payments', LoanPayments::class)->name('loans.payments');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
