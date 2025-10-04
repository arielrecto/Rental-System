<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\RentalController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Internal\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::get('/home' , [HomeController::class, 'index'])->name('home');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('customer')->middleware('auth')->name('customer.')->group(function () {
    Route::prefix('rentals')->as('rentals.')->group(function(){
        Route::get('', [RentalController::class, 'index'])->name('index');
        Route::post('', [RentalController::class, 'store'])->name('store');
        Route::get('/history', [RentalController::class, 'history'])->name('history');
        Route::post('/{id}', [RentalController::class, 'cancel'])->name('cancel');
    });

    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
});


Route::prefix('internal')->middleware('auth')->name('internal.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
