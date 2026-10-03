<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (\Illuminate\Http\Request $request) {
    $isMobile = (bool) preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $request->userAgent() ?? '');
    return redirect()->route($isMobile ? 'menu' : 'dashboard');
});

Route::get('/menu', function (\Illuminate\Http\Request $request) {
    $isMobile = (bool) preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $request->userAgent() ?? '');

    // In desktop mode, do not display the Menu and its content -> redirect to dashboard
    if (! $isMobile && ! $request->has('mobile_preview')) {
        return redirect()->route('dashboard');
    }

    $totalVehicles = \App\Models\Vehicle::count();
    $availableVehicles = \App\Models\Vehicle::where('status', 'available')->count();
    $activeBookings = \App\Models\Booking::whereIn('status', ['confirmed', 'pending'])->count();
    $pendingApprovalsCount = \App\Models\User::where('is_approved', false)->count();

    return view('menu.index', compact('totalVehicles', 'availableVehicles', 'activeBookings', 'pendingApprovalsCount'));
})->middleware(['auth', 'verified'])->name('menu');

Route::get('/dashboard', function () {
    $totalVehicles = \App\Models\Vehicle::where('user_id', auth()->id())->count();
    $availableVehicles = \App\Models\Vehicle::where('user_id', auth()->id())->where('status', 'available')->count();
    $activeBookings = \App\Models\Booking::where('user_id', auth()->id())->whereIn('status', ['confirmed', 'pending'])->count();
    $totalRevenue = \App\Models\Booking::where('user_id', auth()->id())->where('status', 'completed')->sum('total_price') + \App\Models\Booking::where('user_id', auth()->id())->where('status', 'confirmed')->sum('total_price');
    
    $recentBookings = \App\Models\Booking::where('user_id', auth()->id())->with('vehicle')->latest()->take(5)->get();
    $vehicles = \App\Models\Vehicle::where('user_id', auth()->id())->latest()->take(4)->get();
    $myVehicles = \App\Models\Vehicle::where('user_id', auth()->id())->with('vehicleType')->latest()->get();

    return view('dashboard', compact('totalVehicles', 'availableVehicles', 'activeBookings', 'totalRevenue', 'recentBookings', 'vehicles', 'myVehicles'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::patch('/vehicles/{vehicle}/toggle-availability', [VehicleController::class, 'toggleAvailability'])->name('vehicles.toggle-availability');
    Route::delete('/vehicles/{vehicle}/photo', [VehicleController::class, 'deletePhoto'])->name('vehicles.delete-photo');
    Route::patch('/vehicles/{vehicle}/primary-photo', [VehicleController::class, 'setPrimaryPhoto'])->name('vehicles.set-primary-photo');
    Route::get('/vehicles/all-listing', [VehicleController::class, 'allListings'])->name('vehicles.all');
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

