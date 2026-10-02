<?php

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use App\Models\Destination;
use Illuminate\Support\Facades\Storage;


test('authenticated user can view vehicles index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('vehicles.index'));

    $response->assertStatus(200);
});

test('vehicles index displays the owner of the uploaded vehicle', function () {
    $owner = User::factory()->create(['name' => 'Maria Clara', 'email' => 'maria.clara@example.com']);
    $vehicle = Vehicle::create([
        'user_id' => $owner->id,
        'name' => 'Owner Test Car',
        'make' => 'Toyota',
        'model' => 'RAV4',
        'year' => 2024,
        'license_plate' => 'OWNER 888',
        'daily_rate' => 2500.00,
        'status' => 'available',
    ]);

    $authUser = User::factory()->create();
    $response = $this->actingAs($authUser)->get(route('vehicles.index'));

    $response->assertStatus(200);
    $response->assertSee('Maria Clara');
    $response->assertSee('Vehicle Owner');
});

test('authenticated user can view booking calendar', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('calendar.index'));

    $response->assertStatus(200);
});

test('booking calendar filters bookings by selected vehicle id', function () {
    $user = User::factory()->create();
    $vehicle1 = Vehicle::create(['name' => 'Car One', 'make' => 'Toyota', 'model' => 'Vios', 'year' => 2023, 'license_plate' => 'C1 1111', 'daily_rate' => 2000, 'status' => 'available']);
    $vehicle2 = Vehicle::create(['name' => 'Car Two', 'make' => 'Honda', 'model' => 'City', 'year' => 2023, 'license_plate' => 'C2 2222', 'daily_rate' => 2200, 'status' => 'available']);

    $b1 = Booking::create(['booking_code' => 'LNR-FILTER-1', 'vehicle_id' => $vehicle1->id, 'customer_name' => 'Customer One', 'customer_email' => 'c1@example.com', 'customer_phone' => '111', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+2 days')), 'total_days' => 2, 'daily_rate' => 2000, 'total_price' => 4000, 'status' => 'confirmed']);
    $b2 = Booking::create(['booking_code' => 'LNR-FILTER-2', 'vehicle_id' => $vehicle2->id, 'customer_name' => 'Customer Two', 'customer_email' => 'c2@example.com', 'customer_phone' => '222', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+2 days')), 'total_days' => 2, 'daily_rate' => 2200, 'total_price' => 4400, 'status' => 'confirmed']);

    $response = $this->actingAs($user)->get(route('calendar.index', ['vehicle_id' => $vehicle1->id]));

    $response->assertStatus(200);
    $response->assertSee('Customer One');
    $response->assertDontSee('Customer Two');
});



test('can create vehicle data entry with photo upload', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $file = \Illuminate\Http\UploadedFile::fake()->image('fortuner.jpg');

    $response = $this->actingAs($user)->post(route('vehicles.store'), [
        'name' => 'Toyota Fortuner 2.8 V',
        'make' => 'Toyota',
        'model' => 'Fortuner',
        'year' => 2024,
        'license_plate' => 'TEST 9900',
        'color' => 'Black',
        'transmission' => 'Automatic',
        'fuel_type' => 'Diesel',
        'seats' => 7,
        'daily_rate' => 3800.00,
        'status' => 'available',
        'description' => 'Test vehicle description',
        'image' => $file,
    ]);

    $response->assertRedirect(route('vehicles.index'));

    $vehicle = Vehicle::where('license_plate', 'TEST 9900')->first();
    expect($vehicle)->not->toBeNull();
    expect($vehicle->image_path)->not->toBeNull();

    $relativePath = str_replace('storage/', '', $vehicle->image_path);
    Storage::disk('public')->assertExists($relativePath);
});


test('can book a specific car for specific days', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'name' => 'Test Sedan',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2023,
        'license_plate' => 'TEST 1010',
        'daily_rate' => 2000.00,
        'status' => 'available',
    ]);
    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 500.00,
    ]);

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Test Customer',
        'customer_email' => 'customer@example.com',
        'customer_phone' => '09171234567',
        'start_date' => date('Y-m-d', strtotime('+10 days')),
        'end_date' => date('Y-m-d', strtotime('+13 days')),
        'notes' => 'Test booking',
    ]);

    $booking = Booking::where('customer_email', 'customer@example.com')->first();
    expect($booking)->not->toBeNull();
    expect($booking->total_days)->toBe(3);
    expect((float)$booking->total_price)->toBe(6500.00); // 3 * 2000 + 500

    $response->assertRedirect(route('bookings.show', $booking->id));
});

test('prevents double booking for the same vehicle on overlapping days', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'name' => 'Test SUV',
        'make' => 'Mitsubishi',
        'model' => 'Montero',
        'year' => 2024,
        'license_plate' => 'TEST 2020',
        'daily_rate' => 3000.00,
        'status' => 'available',
    ]);
    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 0.00,
    ]);

    Booking::create([
        'booking_code' => 'LNR-EXISTING-1',
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Existing Customer',
        'customer_email' => 'existing@example.com',
        'customer_phone' => '09170001111',
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-05',
        'total_days' => 4,
        'daily_rate' => 3000.00,
        'total_price' => 12000.00,
        'status' => 'confirmed',
    ]);

    // Try booking overlapping dates
    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Conflict Customer',
        'customer_email' => 'conflict@example.com',
        'customer_phone' => '09179998888',
        'start_date' => '2026-11-03',
        'end_date' => '2026-11-07',
    ]);

    $response->assertSessionHasErrors('start_date');
});
