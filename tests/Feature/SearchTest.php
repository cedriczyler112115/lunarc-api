<?php

use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;

test('guest is redirected to login when visiting search page', function () {
    $response = $this->get(route('search.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can view search page', function () {
    $user = User::factory()->create(['is_approved' => true]);
    $vehicleType = VehicleType::create([
        'name' => 'SUV',
        'slug' => 'suv',
    ]);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Toyota Fortuner',
        'make' => 'Toyota',
        'model' => 'Fortuner',
        'year' => 2024,
        'license_plate' => 'ABC 1234',
        'daily_rate' => 3500.00,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->get(route('search.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Search/Index')
        ->has('vehicles', 0)
        ->where('hasSearched', false)
        ->has('vehicleTypes')
        ->has('filters')
    );
});

test('search filters vehicles by vehicle type', function () {
    $user = User::factory()->create(['is_approved' => true]);
    $suv = VehicleType::create(['name' => 'SUV', 'slug' => 'suv']);
    $sedan = VehicleType::create(['name' => 'Sedan', 'slug' => 'sedan']);

    $vehicleSuv = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $suv->id,
        'name' => 'Toyota Fortuner',
        'make' => 'Toyota',
        'model' => 'Fortuner',
        'year' => 2024,
        'license_plate' => 'SUV 1111',
        'daily_rate' => 3500.00,
        'status' => 'available',
    ]);
    $vehicleSedan = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $sedan->id,
        'name' => 'Toyota Vios',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2023,
        'license_plate' => 'SED 2222',
        'daily_rate' => 2000.00,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->get(route('search.index', [
        'vehicle_type_id' => $suv->id,
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Search/Index')
        ->has('vehicles', 1)
        ->where('vehicles.0.id', $vehicleSuv->id)
    );
});

test('search filters vehicles by availability dates and times', function () {
    $owner = User::factory()->create(['is_approved' => true, 'name' => 'Owner Maria']);
    $user = User::factory()->create(['is_approved' => true]);

    $vehicle1 = Vehicle::create([
        'user_id' => $owner->id,
        'name' => 'Car One',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2023,
        'license_plate' => 'AAA 1111',
        'daily_rate' => 2000.00,
        'status' => 'available',
    ]);
    $vehicle2 = Vehicle::create([
        'user_id' => $owner->id,
        'name' => 'Car Two',
        'make' => 'Honda',
        'model' => 'City',
        'year' => 2023,
        'license_plate' => 'BBB 2222',
        'daily_rate' => 2200.00,
        'status' => 'available',
    ]);

    // Create a confirmed booking on vehicle1 for 2026-11-01 to 2026-11-03
    Booking::create([
        'booking_code' => Booking::generateBookingCode(),
        'vehicle_id' => $vehicle1->id,
        'user_id' => $owner->id,
        'customer_name' => 'John Doe',
        'customer_phone' => '09123456789',
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-03',
        'pickup_time' => '10:00',
        'return_time' => '17:00',
        'total_days' => 3,
        'daily_rate' => 2000.00,
        'total_price' => 6000.00,
        'status' => 'confirmed',
    ]);

    // Search overlapping dates on vehicle1
    $response = $this->actingAs($user)->get(route('search.index', [
        'start_date' => '2026-11-02',
        'end_date' => '2026-11-02',
        'pickup_time' => '10:00',
        'return_time' => '12:00',
    ]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Search/Index')
        ->has('vehicles', 1)
        ->where('vehicles.0.id', $vehicle2->id)
        ->where('vehicles.0.user.name', 'Owner Maria')
    );
});
