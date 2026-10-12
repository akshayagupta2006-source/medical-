<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CaseWebController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Medical Case Management Application Web Routes.
|
*/

// Root redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Case Management Web Routes
    Route::get('/cases', [CaseWebController::class, 'index'])->name('cases.index');
    Route::get('/cases/create', [CaseWebController::class, 'create'])->name('cases.create');
    Route::post('/cases', [CaseWebController::class, 'store'])->name('cases.store');
    Route::get('/cases/{case}', [CaseWebController::class, 'show'])->name('cases.show');
    Route::get('/cases/{case}/edit', [CaseWebController::class, 'edit'])->name('cases.edit');
    Route::put('/cases/{case}', [CaseWebController::class, 'update'])->name('cases.update');
    Route::delete('/cases/{case}', [CaseWebController::class, 'destroy'])->name('cases.destroy');
});
