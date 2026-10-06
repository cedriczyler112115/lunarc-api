<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleTypeController;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    $isMobile = (bool) preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $request->userAgent() ?? '');

    return redirect()->route($isMobile ? 'menu' : 'dashboard');
});

Route::get('/menu', function (Request $request) {
    $isMobile = (bool) preg_match('/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i', $request->userAgent() ?? '');

    if (! $isMobile && ! $request->has('mobile_preview')) {
        return redirect()->route('dashboard');
    }

    $user = auth()->user();
    $userVehicleIds = Vehicle::where('user_id', $user->id)->pluck('id');

    $totalVehicles = Vehicle::where('user_id', $user->id)->count();
    $availableVehicles = Vehicle::where('user_id', $user->id)->where('status', 'available')->count();
    $activeBookings = Booking::where(function ($q) use ($user, $userVehicleIds) {
        $q->where('user_id', $user->id)
            ->orWhereIn('vehicle_id', $userVehicleIds);
    })->where('status', 'confirmed')->count();

    $pendingApprovalsCount = User::where('is_approved', false)->count();

    return Inertia::render('Menu/Index', [
        'totalVehicles' => $totalVehicles,
        'availableVehicles' => $availableVehicles,
        'activeBookings' => $activeBookings,
        'pendingApprovalsCount' => $pendingApprovalsCount,
    ]);
})->middleware(['auth', 'verified'])->name('menu');

Route::get('/dashboard', function () {
    $user = auth()->user();
    $userVehicleIds = Vehicle::where('user_id', $user->id)->pluck('id');

    $totalVehicles = Vehicle::where('user_id', $user->id)->count();
    $availableVehicles = Vehicle::where('user_id', $user->id)->where('status', 'available')->count();
    $activeBookings = Booking::where(function ($q) use ($user, $userVehicleIds) {
        $q->where('user_id', $user->id)
            ->orWhereIn('vehicle_id', $userVehicleIds);
    })->where('status', 'confirmed')->count();

    $totalRevenue = Booking::where(function ($q) use ($user, $userVehicleIds) {
        $q->where('user_id', $user->id)
            ->orWhereIn('vehicle_id', $userVehicleIds);
    })->whereIn('status', ['completed', 'confirmed'])->sum('total_price');

    $recentBookings = Booking::where(function ($q) use ($user, $userVehicleIds) {
        $q->where('user_id', $user->id)
            ->orWhereIn('vehicle_id', $userVehicleIds);
    })
        ->with(['vehicle', 'destinationModel'])
        ->latest()
        ->take(5)
        ->get();

    $vehicles = Vehicle::where('user_id', $user->id)
        ->latest()
        ->take(4)
        ->get();

    $myVehicles = Vehicle::where('user_id', $user->id)
        ->with('vehicleType')
        ->latest()
        ->get();

    $monthlyBookings = [];
    for ($i = 11; $i >= 0; $i--) {
        $targetDate = now()->subMonths($i);
        $y = $targetDate->year;
        $m = $targetDate->month;
        $monthShort = $targetDate->format('M');
        $monthFull = $targetDate->format('M Y');

        $count = Booking::where(function ($q) use ($user, $userVehicleIds) {
            $q->where('user_id', $user->id)
                ->orWhereIn('vehicle_id', $userVehicleIds);
        })
            ->whereYear('start_date', $y)
            ->whereMonth('start_date', $m)
            ->count();

        $revenue = (float) Booking::where(function ($q) use ($user, $userVehicleIds) {
            $q->where('user_id', $user->id)
                ->orWhereIn('vehicle_id', $userVehicleIds);
        })
            ->whereYear('start_date', $y)
            ->whereMonth('start_date', $m)
            ->whereIn('status', ['completed', 'confirmed'])
            ->sum('total_price');

        $monthlyBookings[] = [
            'month' => $monthShort,
            'label' => $monthFull,
            'year' => $y,
            'count' => $count,
            'revenue' => $revenue,
        ];
    }

    return Inertia::render('Dashboard', [
        'totalVehicles' => $totalVehicles,
        'availableVehicles' => $availableVehicles,
        'activeBookings' => $activeBookings,
        'totalRevenue' => (float) $totalRevenue,
        'recentBookings' => $recentBookings,
        'vehicles' => $vehicles,
        'myVehicles' => $myVehicles,
        'monthlyBookings' => $monthlyBookings,
    ]);
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
    Route::get('/income', [IncomeController::class, 'index'])->name('income.index');

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
