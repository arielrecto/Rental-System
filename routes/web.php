<?php


use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KioskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\RentalController;
use App\Http\Controllers\Internal\ReportController;
use App\Http\Controllers\Internal\PaymentController;
use App\Http\Controllers\Internal\VehicleController;
use App\Http\Controllers\Internal\DashboardController;
use App\Http\Controllers\Internal\RentalOrderController;
use App\Http\Controllers\Internal\PaymentAcountController;
use App\Http\Controllers\Internal\UserManagementController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Internal\RentalOrderController as InternalRentalOrderController;
use App\Http\Controllers\MaintenanceVehicleRequestController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::get('/home', [HomeController::class, 'index'])->name('home');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('customer')->middleware('auth')->name('customer.')->group(function () {
    Route::prefix('rentals')->as('rentals.')->group(function () {
        Route::get('', [RentalController::class, 'index'])->name('index');
        Route::post('', [RentalController::class, 'store'])->name('store');
        Route::get('/history', [RentalController::class, 'history'])->name('history');
        Route::post('/{id}', [RentalController::class, 'cancel'])->name('cancel');
    });

    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('create', [CustomerPaymentController::class, 'create'])->name('create');
        Route::get('history', [CustomerPaymentController::class, 'history'])->name('history');
        Route::post('', [CustomerPaymentController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CustomerPaymentController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [CustomerPaymentController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [CustomerPaymentController::class, 'destroy'])->name('delete');
    });

    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
});


Route::prefix('internal')->middleware('auth')->name('internal.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('vehicles', VehicleController::class);
    Route::prefix('rental-orders')->as('rental-orders.')->group(function () {
        Route::get('active', [RentalOrderController::class, 'activeRentals'])->name('active');
    });
    Route::resource('rental-orders', InternalRentalOrderController::class);
    Route::resource('user-management', UserManagementController::class);
    Route::prefix('report')->as('report.')->group(function () {
        Route::get('revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('rental-analytics', [ReportController::class, 'rentalAnalytic'])->name('rental-analytics');
        Route::get('vehicle', [ReportController::class, 'vehicle'])->name('vehicle');
    });


    Route::prefix('maintenance')->as('maintenance.')->group(function () {
        Route::get('mark-as-completed/{id}', [MaintenanceVehicleRequestController::class, 'markAsCompleted'])->name('mark-as-completed');
        Route::get('mark-as-on-going/{id}', [MaintenanceVehicleRequestController::class, 'markAsOnGoing'])->name('mark-as-on-going');
    });

    Route::resource('payment-accounts', PaymentAcountController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('maintenance', MaintenanceVehicleRequestController::class);
});


Route::prefix('kiosk')->as('kiosk.')->group(function () {
    Route::get('', [KioskController::class, 'index'])->name('index');
    Route::post('start-session', [KioskController::class, 'startSession'])->name('start-session');
    Route::get('session/{sessionId}/rental-order/{rentalOrderId}', [KioskController::class, 'session'])->name('session');
    Route::post('session/{sessionToken}/location', [KioskController::class, 'updateLocation'])->name('session.update-location');
    Route::post('session/{sessionToken}/close', [KioskController::class, 'closeSession'])->name('session.close');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
