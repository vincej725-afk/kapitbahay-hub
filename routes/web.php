<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\OwnerController;
use App\Http\Controllers\Web\StaffOrderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Staff Website using Session Authentication - Laravel Breeze style)
|--------------------------------------------------------------------------
| Implements SDLC Phase 3 and Question 2(c): Session-based login for Staff and Owner
*/

// Home route: Redirects to dashboard if authenticated, or renders landing page
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return $user->role === 'owner'
            ? redirect()->route('owner.dashboard')
            : redirect()->route('staff.orders.index');
    }
    return view('welcome');
})->name('home');

// Guest authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Web Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ----------------------------------------------------
    // Staff Portal Routes
    // Tasks: Record walk-in orders, update order status for their own branch
    // ----------------------------------------------------
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/orders', [StaffOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [StaffOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [StaffOrderController::class, 'store'])->name('orders.store');
        Route::patch('/orders/{order}/status', [StaffOrderController::class, 'updateStatus'])->name('orders.status');
    });

    // ----------------------------------------------------
    // Owner Portal Routes
    // Tasks: Manage prices, manage staff accounts, view orders and sales of all branches
    // Fixes Problem M1 (N+1 queries) using eager loading and pagination
    // ----------------------------------------------------
    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
        Route::get('/services', [OwnerController::class, 'services'])->name('services');
        Route::patch('/services/{service}', [OwnerController::class, 'updateService'])->name('services.update');
        Route::get('/staff', [OwnerController::class, 'staff'])->name('staff');
        Route::post('/staff', [OwnerController::class, 'storeStaff'])->name('staff.store');
    });
});
