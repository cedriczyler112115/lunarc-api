<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    $totalVehicles = \App\Models\Vehicle::count();
    $availableVehicles = \App\Models\Vehicle::where('status', 'available')->count();
    $activeBookings = \App\Models\Booking::whereIn('status', ['confirmed', 'pending'])->count();
    $totalRevenue = \App\Models\Booking::where('status', 'completed')->sum('total_price') + \App\Models\Booking::where('status', 'confirmed')->sum('total_price');
    
    $recentBookings = \App\Models\Booking::with('vehicle')->latest()->take(5)->get();
    $vehicles = \App\Models\Vehicle::latest()->take(4)->get();

    return view('dashboard', compact('totalVehicles', 'availableVehicles', 'activeBookings', 'totalRevenue', 'recentBookings', 'vehicles'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::delete('/vehicles/{vehicle}/photo', [VehicleController::class, 'deletePhoto'])->name('vehicles.delete-photo');
    Route::patch('/vehicles/{vehicle}/primary-photo', [VehicleController::class, 'setPrimaryPhoto'])->name('vehicles.set-primary-photo');
    Route::resource('vehicles', VehicleController::class);
    Route::patch('/destinations/{destination}/price', [DestinationController::class, 'updatePrice'])->name('destinations.update-price');
    Route::patch('/destinations/{destination}/rates', [DestinationController::class, 'updateRates'])->name('destinations.update-rates');
    Route::resource('destinations', DestinationController::class);
    Route::resource('bookings', BookingController::class);
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

    // Dynamic Vehicle Types
    Route::post('/vehicle-types', [VehicleTypeController::class, 'store'])->name('vehicle-types.store');
    Route::delete('/vehicle-types/{vehicleType}', [VehicleTypeController::class, 'destroy'])->name('vehicle-types.destroy');

    // Admin User Approvals
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users/{user}/approve', [AdminUserController::class, 'approve'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';

