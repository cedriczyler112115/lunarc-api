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

test('vehicles index displays only the vehicles of the logged in user', function () {
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

    $authUser = User::factory()->create(['name' => 'Juan Dela Cruz']);
    $myVehicle = Vehicle::create([
        'user_id' => $authUser->id,
        'name' => 'My Personal Fleet Car',
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2024,
        'license_plate' => 'MY 7777',
        'daily_rate' => 2200.00,
        'status' => 'available',
    ]);

    // In My Fleet (/vehicles), only logged-in user's vehicle is displayed
    $response = $this->actingAs($authUser)->get(route('vehicles.index'));
    $response->assertStatus(200);
    $response->assertSee('My Personal Fleet Car');
    $response->assertDontSee('Owner Test Car');

    // In All Listing (/vehicles/all-listing), all vehicles are displayed with owner info
    $allListingResponse = $this->actingAs($authUser)->get(route('vehicles.all'));
    $allListingResponse->assertStatus(200);
    $allListingResponse->assertSee('Owner Test Car');
    $allListingResponse->assertSee('Maria Clara');
    $allListingResponse->assertSee('My Personal Fleet Car');
});

test('bookings index displays only bookings of the logged in user', function () {
    $userA = User::factory()->create(['name' => 'User Alpha']);
    $userB = User::factory()->create(['name' => 'User Beta']);

    $vehicle = Vehicle::create([
        'name' => 'Booking Fleet Vehicle',
        'make' => 'Toyota',
        'model' => 'Innova',
        'year' => 2024,
        'license_plate' => 'BKG 1234',
        'daily_rate' => 3000.00,
        'status' => 'available',
    ]);

    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 1000.00,
    ]);

    $bookingA = Booking::create([
        'booking_code' => 'LNR-ALPHA-01',
        'vehicle_id' => $vehicle->id,
        'user_id' => $userA->id,
        'destination_id' => $destination->id,
        'destination' => 'Butuan City',
        'customer_name' => 'Customer of Alpha',
        'customer_phone' => '09170001111',
        'pickup_location' => 'Airport',
        'pickup_time' => '08:00',
        'return_time' => '17:00',
        'start_date' => date('Y-m-d', strtotime('+5 days')),
        'end_date' => date('Y-m-d', strtotime('+6 days')),
        'total_days' => 1,
        'daily_rate' => 3000.00,
        'total_price' => 3000.00,
        'status' => 'confirmed',
    ]);

    $bookingB = Booking::create([
        'booking_code' => 'LNR-BETA-02',
        'vehicle_id' => $vehicle->id,
        'user_id' => $userB->id,
        'destination_id' => $destination->id,
        'destination' => 'Butuan City',
        'customer_name' => 'Customer of Beta',
        'customer_phone' => '09170002222',
        'pickup_location' => 'Airport',
        'pickup_time' => '08:00',
        'return_time' => '17:00',
        'start_date' => date('Y-m-d', strtotime('+10 days')),
        'end_date' => date('Y-m-d', strtotime('+11 days')),
        'total_days' => 1,
        'daily_rate' => 3000.00,
        'total_price' => 3000.00,
        'status' => 'confirmed',
    ]);

    $response = $this->actingAs($userA)->get(route('bookings.index'));

    $response->assertStatus(200);
    $response->assertSee('LNR-ALPHA-01');
    $response->assertSee('Customer of Alpha');
    $response->assertDontSee('LNR-BETA-02');
    $response->assertDontSee('Customer of Beta');
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

    $b1 = Booking::create(['booking_code' => 'LNR-FILTER-1', 'user_id' => $user->id, 'vehicle_id' => $vehicle1->id, 'customer_name' => 'Customer One', 'customer_email' => 'c1@example.com', 'customer_phone' => '111', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+2 days')), 'total_days' => 2, 'daily_rate' => 2000, 'total_price' => 4000, 'status' => 'confirmed']);
    $b2 = Booking::create(['booking_code' => 'LNR-FILTER-2', 'user_id' => $user->id, 'vehicle_id' => $vehicle2->id, 'customer_name' => 'Customer Two', 'customer_email' => 'c2@example.com', 'customer_phone' => '222', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+2 days')), 'total_days' => 2, 'daily_rate' => 2200, 'total_price' => 4400, 'status' => 'confirmed']);

    $response = $this->actingAs($user)->get(route('calendar.index', ['vehicle_id' => $vehicle1->id]));

    $response->assertStatus(200);
    $response->assertSee('Customer One');
    $response->assertDontSee('Customer Two');
});

test('booking calendar filters bookings by user id and defaults to logged in user', function () {
    $userA = User::factory()->create(['name' => 'User A']);
    $userB = User::factory()->create(['name' => 'User B']);
    $vehicle = Vehicle::create(['name' => 'Test Car', 'make' => 'Toyota', 'model' => 'Vios', 'year' => 2023, 'license_plate' => 'USER 100', 'daily_rate' => 2000, 'status' => 'available']);

    Booking::create(['booking_code' => 'LNR-UA', 'vehicle_id' => $vehicle->id, 'user_id' => $userA->id, 'customer_name' => 'Booking For A', 'customer_email' => 'a@example.com', 'customer_phone' => '111', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+1 day')), 'total_days' => 1, 'daily_rate' => 2000, 'total_price' => 2000, 'status' => 'confirmed']);
    Booking::create(['booking_code' => 'LNR-UB', 'vehicle_id' => $vehicle->id, 'user_id' => $userB->id, 'customer_name' => 'Booking For B', 'customer_email' => 'b@example.com', 'customer_phone' => '222', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+1 day')), 'total_days' => 1, 'daily_rate' => 2000, 'total_price' => 2000, 'status' => 'confirmed']);

    // Default logged in user A sees User A's booking but not B's
    $responseDefault = $this->actingAs($userA)->get(route('calendar.index'));
    $responseDefault->assertStatus(200);
    $responseDefault->assertSee('Booking For A');
    $responseDefault->assertDontSee('Booking For B');

    // Filtering explicitly for All Users sees both
    $responseAll = $this->actingAs($userA)->get(route('calendar.index', ['user_id' => 'all']));
    $responseAll->assertStatus(200);
    $responseAll->assertSee('Booking For A');
    $responseAll->assertSee('Booking For B');

    // Filtering explicitly for User B sees B's booking but not A's
    $responseUserB = $this->actingAs($userA)->get(route('calendar.index', ['user_id' => $userB->id]));
    $responseUserB->assertStatus(200);
    $responseUserB->assertSee('Booking For B');
    $responseUserB->assertDontSee('Booking For A');
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
        'pickup_location' => 'Bancasi Airport',
        'pickup_time' => '08:00',
        'return_time' => '08:00',
        'start_date' => date('Y-m-d', strtotime('+10 days')),
        'end_date' => date('Y-m-d', strtotime('+13 days')),
        'notes' => 'Test booking',
    ]);

    $booking = Booking::where('customer_name', 'Test Customer')->first();
    expect($booking)->not->toBeNull();
    expect($booking->total_days)->toBe(3);
    expect((float)$booking->total_price)->toBe(1500.00); // 3 days * 500 destination rate

    $response->assertRedirect(route('bookings.show', $booking->id));
});

test('computes 24-hour days and excess hours rate up to 5 hours max', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'name' => '24h Test Car',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => '24H 999',
        'daily_rate' => 2000.00,
        'status' => 'available',
    ]);
    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 1000.00,
    ]);

    // 27 hours rental (1 day + 3 excess hours) -> 1 day * 1000 + 3 * 200 = 1600
    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => '27 Hours Renter',
        'customer_phone' => '09170001111',
        'pickup_location' => 'Bancasi Airport',
        'pickup_time' => '08:00',
        'return_time' => '11:00',
        'start_date' => '2026-11-10',
        'end_date' => '2026-11-11',
    ]);

    $booking = Booking::where('customer_name', '27 Hours Renter')->first();
    expect($booking)->not->toBeNull();
    expect($booking->total_days)->toBe(1);
    expect((float)$booking->total_price)->toBe(1600.00); // 1 day * 1000 + 3 * 200
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
        'pickup_location' => 'Airport',
        'pickup_time' => '08:00',
        'return_time' => '13:00',
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-02',
        'total_days' => 2,
        'daily_rate' => 3000.00,
        'total_price' => 6000.00,
        'status' => 'confirmed',
    ]);

    // Attempting 2:30 PM pickup on Nov 2 (Previous returns 1:00 PM + 2h buffer = 3:00 PM) -> CONFLICT
    $responseConflict = $this->actingAs($user)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Conflict Customer',
        'customer_phone' => '09179998888',
        'pickup_location' => 'Bancasi Airport',
        'pickup_time' => '14:30',
        'return_time' => '17:00',
        'start_date' => '2026-11-02',
        'end_date' => '2026-11-03',
    ]);
    $responseConflict->assertSessionHasErrors('start_date');

    // Attempting 3:00 PM pickup on Nov 2 -> SUCCESS (Carwash buffer ended at 3:00 PM)
    $responseSuccess = $this->actingAs($user)->post(route('bookings.store'), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Valid Same Day Customer',
        'customer_phone' => '09178887777',
        'pickup_location' => 'Bancasi Airport',
        'pickup_time' => '15:00',
        'return_time' => '18:00',
        'start_date' => '2026-11-02',
        'end_date' => '2026-11-03',
    ]);
    $responseSuccess->assertSessionHasNoErrors();
    $booking = Booking::where('customer_name', 'Valid Same Day Customer')->first();
    expect($booking)->not->toBeNull();
});

test('can delete photo from vehicle entry', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'name' => 'Photo Delete Test Car',
        'make' => 'Toyota',
        'model' => 'Innova',
        'year' => 2024,
        'license_plate' => 'DEL 999',
        'daily_rate' => 3000,
        'status' => 'available',
        'image_path' => 'storage/vehicles/photo1.jpg',
        'images' => ['storage/vehicles/photo1.jpg', 'storage/vehicles/photo2.jpg'],
    ]);

    // Delete primary photo
    $response = $this->actingAs($user)->delete(route('vehicles.delete-photo', $vehicle->id), [
        'image_path' => 'storage/vehicles/photo1.jpg',
    ]);

    $response->assertRedirect();
    $vehicle->refresh();

    expect($vehicle->image_path)->toBe('storage/vehicles/photo2.jpg');
    expect($vehicle->images)->toBe(['storage/vehicles/photo2.jpg']);
});

test('can change primary cover photo to another existing gallery photo', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'name' => 'Primary Swap Car',
        'make' => 'Honda',
        'model' => 'CR-V',
        'year' => 2024,
        'license_plate' => 'SWAP 888',
        'daily_rate' => 3500,
        'status' => 'available',
        'image_path' => 'storage/vehicles/img1.jpg',
        'images' => ['storage/vehicles/img1.jpg', 'storage/vehicles/img2.jpg', 'storage/vehicles/img3.jpg'],
    ]);

    $response = $this->actingAs($user)->patch(route('vehicles.set-primary-photo', $vehicle->id), [
        'image_path' => 'storage/vehicles/img3.jpg',
    ]);

    $response->assertRedirect();
    $vehicle->refresh();

    expect($vehicle->image_path)->toBe('storage/vehicles/img3.jpg');
    expect($vehicle->images[0])->toBe('storage/vehicles/img3.jpg');
});

test('dashboard displays logged-in user vehicle cards with availability toggle', function () {
    $user = User::factory()->create(['name' => 'Host User']);
    $otherUser = User::factory()->create(['name' => 'Other Host']);

    $myCar = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Host Montero Sport',
        'make' => 'Mitsubishi',
        'model' => 'Montero',
        'year' => 2024,
        'license_plate' => 'HOST 111',
        'daily_rate' => 3500,
        'status' => 'available',
    ]);

    $otherCar = Vehicle::create([
        'user_id' => $otherUser->id,
        'name' => 'Other Host Sedan',
        'make' => 'Nissan',
        'model' => 'Almera',
        'year' => 2024,
        'license_plate' => 'OTHER 222',
        'daily_rate' => 2000,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('My Fleet Availability');
    $response->assertSee('Host Montero Sport');
    $response->assertSee('HOST 111');
    $response->assertDontSee('OTHER 222');
});

test('vehicle availability can be toggled on and off via toggle-availability endpoint', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Toggle Car',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => 'TOGGLE 999',
        'daily_rate' => 2000,
        'status' => 'available',
    ]);

    // Toggle from available -> out_of_service (JSON)
    $response = $this->actingAs($user)->patchJson(route('vehicles.toggle-availability', $vehicle->id), [
        'status' => 'out_of_service',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true, 'status' => 'out_of_service', 'is_available' => false]);
    $vehicle->refresh();
    expect($vehicle->status)->toBe('out_of_service');

    // Toggle back to available (without payload)
    $responseBack = $this->actingAs($user)->patchJson(route('vehicles.toggle-availability', $vehicle->id));
    $responseBack->assertStatus(200);
    $responseBack->assertJson(['success' => true, 'status' => 'available', 'is_available' => true]);
    $vehicle->refresh();
    expect($vehicle->status)->toBe('available');
});
