<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;

test('booking can be updated to completed with actual income', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'Sedan', 'capacity' => 5]);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Toyota Vios',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => 'ABC 1234',
        'daily_rate' => 2000,
        'status' => 'rented',
    ]);

    $destination = Destination::create([
        'city' => 'Davao City',
        'province' => 'Davao del Sur',
        'region' => 'Region XI',
        'base_price' => 3000,
    ]);

    $booking = Booking::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'booking_code' => 'BK-TEST-001',
        'customer_name' => 'Alice Guo',
        'customer_phone' => '09123456789',
        'start_date' => now()->startOfMonth()->toDateString(),
        'end_date' => now()->startOfMonth()->addDays(2)->toDateString(),
        'pickup_time' => '09:00',
        'return_time' => '17:00',
        'pickup_location' => 'Airport',
        'destination' => 'Davao City',
        'destination_rate' => 3000,
        'total_days' => 2,
        'daily_rate' => 2000,
        'total_price' => 6000,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($user)->patch(route('bookings.update', $booking->id), [
        'status' => 'completed',
        'actual_income' => 5800,
    ]);

    $response->assertRedirect(route('bookings.show', $booking->id));

    $booking->refresh();
    expect($booking->status)->toBe('completed')
        ->and($booking->actual_income)->toBe(5800);
});

test('authenticated user can view income index page with total income and pagination', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'SUV', 'capacity' => 7]);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Mitsubishi Montero',
        'make' => 'Mitsubishi',
        'model' => 'Montero',
        'year' => 2024,
        'license_plate' => 'XYZ 9999',
        'daily_rate' => 3500,
        'status' => 'available',
    ]);

    $destination = Destination::create([
        'city' => 'Cagayan de Oro',
        'province' => 'Misamis Oriental',
        'region' => 'Region X',
        'base_price' => 4000,
    ]);

    // Create completed booking with actual income
    Booking::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'booking_code' => 'BK-INC-001',
        'customer_name' => 'John Doe',
        'customer_phone' => '09123456789',
        'start_date' => now()->startOfMonth()->toDateString(),
        'end_date' => now()->startOfMonth()->addDays(3)->toDateString(),
        'pickup_time' => '08:00',
        'return_time' => '16:00',
        'pickup_location' => 'Hub',
        'destination' => 'Cagayan de Oro',
        'destination_rate' => 4000,
        'total_days' => 3,
        'daily_rate' => 3500,
        'total_price' => 12000,
        'actual_income' => 11500,
        'status' => 'completed',
    ]);

    // Create a confirmed (active/non-completed) booking that should NOT appear in income index
    Booking::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'booking_code' => 'BK-CONF-002',
        'customer_name' => 'Jane Smith',
        'customer_phone' => '09123456789',
        'start_date' => now()->startOfMonth()->toDateString(),
        'end_date' => now()->startOfMonth()->addDays(1)->toDateString(),
        'pickup_time' => '08:00',
        'return_time' => '16:00',
        'pickup_location' => 'Hub',
        'destination' => 'Cagayan de Oro',
        'destination_rate' => 4000,
        'total_days' => 1,
        'daily_rate' => 3500,
        'total_price' => 4000,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($user)->get(route('income.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Income/Index')
        ->has('incomes.data', 1)
        ->where('totalIncome', 11500)
        ->where('totalCompletedBookings', 1)
        ->where('incomes.data.0.booking_code', 'BK-INC-001')
    );
});

test('income index can filter by month and vehicle and recalculate total income', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'Van', 'capacity' => 12]);

    $vehicleA = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Toyota HiAce A',
        'make' => 'Toyota',
        'model' => 'HiAce',
        'year' => 2024,
        'license_plate' => 'VAN 001',
        'daily_rate' => 4000,
        'status' => 'available',
    ]);

    $vehicleB = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Toyota HiAce B',
        'make' => 'Toyota',
        'model' => 'HiAce',
        'year' => 2024,
        'license_plate' => 'VAN 002',
        'daily_rate' => 4000,
        'status' => 'available',
    ]);

    $destination = Destination::create([
        'city' => 'General Santos',
        'province' => 'South Cotabato',
        'region' => 'Region XII',
        'base_price' => 5000,
    ]);

    // Trip 1: Vehicle A in current month (actual_income 10,000)
    Booking::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicleA->id,
        'destination_id' => $destination->id,
        'booking_code' => 'BK-OCT-A',
        'customer_name' => 'Customer A',
        'customer_phone' => '09111111111',
        'start_date' => '2026-10-05',
        'end_date' => '2026-10-07',
        'pickup_time' => '08:00',
        'return_time' => '16:00',
        'pickup_location' => 'Hub',
        'destination' => 'General Santos',
        'destination_rate' => 5000,
        'total_days' => 2,
        'daily_rate' => 4000,
        'total_price' => 10000,
        'actual_income' => 9500,
        'status' => 'completed',
    ]);

    // Trip 2: Vehicle B in current month (actual_income 15,000)
    Booking::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicleB->id,
        'destination_id' => $destination->id,
        'booking_code' => 'BK-OCT-B',
        'customer_name' => 'Customer B',
        'customer_phone' => '09222222222',
        'start_date' => '2026-10-10',
        'end_date' => '2026-10-13',
        'pickup_time' => '08:00',
        'return_time' => '16:00',
        'pickup_location' => 'Hub',
        'destination' => 'General Santos',
        'destination_rate' => 5000,
        'total_days' => 3,
        'daily_rate' => 4000,
        'total_price' => 15000,
        'actual_income' => 15000,
        'status' => 'completed',
    ]);

    // Trip 3: Vehicle A in previous month (September) (actual_income 8,000)
    Booking::create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicleA->id,
        'destination_id' => $destination->id,
        'booking_code' => 'BK-SEP-A',
        'customer_name' => 'Customer C',
        'customer_phone' => '09333333333',
        'start_date' => '2026-09-15',
        'end_date' => '2026-09-17',
        'pickup_time' => '08:00',
        'return_time' => '16:00',
        'pickup_location' => 'Hub',
        'destination' => 'General Santos',
        'destination_rate' => 5000,
        'total_days' => 2,
        'daily_rate' => 4000,
        'total_price' => 8000,
        'actual_income' => 8000,
        'status' => 'completed',
    ]);

    // Test 1: No filter -> Total is 9500 + 15000 + 8000 = 32500
    $respAll = $this->actingAs($user)->get(route('income.index'));
    $respAll->assertStatus(200);
    $respAll->assertInertia(fn ($page) => $page->where('totalIncome', 32500)->has('incomes.data', 3));

    // Test 2: Filter by Month 2026-10 -> Total is 9500 + 15000 = 24500
    $respMonth = $this->actingAs($user)->get(route('income.index', ['month' => '2026-10']));
    $respMonth->assertStatus(200);
    $respMonth->assertInertia(fn ($page) => $page->where('totalIncome', 24500)->has('incomes.data', 2));

    // Test 3: Filter by Vehicle A -> Total is 9500 + 8000 = 17500
    $respVehicle = $this->actingAs($user)->get(route('income.index', ['vehicle_id' => $vehicleA->id]));
    $respVehicle->assertStatus(200);
    $respVehicle->assertInertia(fn ($page) => $page->where('totalIncome', 17500)->has('incomes.data', 2));

    // Test 4: Filter by Vehicle A and Month 2026-10 -> Total is 9500
    $respBoth = $this->actingAs($user)->get(route('income.index', ['vehicle_id' => $vehicleA->id, 'month' => '2026-10']));
    $respBoth->assertStatus(200);
    $respBoth->assertInertia(fn ($page) => $page->where('totalIncome', 9500)->has('incomes.data', 1));
});
