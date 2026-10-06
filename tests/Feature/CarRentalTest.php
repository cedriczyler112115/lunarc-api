<?php

use App\Models\Booking;
use App\Models\Destination;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\UploadedFile;
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
    $response->assertInertia(fn ($page) => $page->component('Vehicles/Index')->where('vehicles.0.name', 'My Personal Fleet Car'));

    // In All Listing (/vehicles/all-listing), all vehicles are displayed with owner info
    $allListingResponse = $this->actingAs($authUser)->get(route('vehicles.all'));
    $allListingResponse->assertStatus(200);
    $allListingResponse->assertInertia(fn ($page) => $page->component('Vehicles/AllListing')->has('vehicles', 2));
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
    $response->assertInertia(fn ($page) => $page->component('Bookings/Index')->has('bookings.data', 1)->where('bookings.data.0.booking_code', 'LNR-ALPHA-01'));
});

test('authenticated user can view booking calendar', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('calendar.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Calendar/Index'));
});

test('booking calendar filters bookings by selected vehicle id', function () {
    $user = User::factory()->create();
    $vehicle1 = Vehicle::create(['name' => 'Car One', 'make' => 'Toyota', 'model' => 'Vios', 'year' => 2023, 'license_plate' => 'C1 1111', 'daily_rate' => 2000, 'status' => 'available']);
    $vehicle2 = Vehicle::create(['name' => 'Car Two', 'make' => 'Honda', 'model' => 'City', 'year' => 2023, 'license_plate' => 'C2 2222', 'daily_rate' => 2200, 'status' => 'available']);

    $b1 = Booking::create(['booking_code' => 'LNR-FILTER-1', 'user_id' => $user->id, 'vehicle_id' => $vehicle1->id, 'customer_name' => 'Customer One', 'customer_email' => 'c1@example.com', 'customer_phone' => '111', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+2 days')), 'total_days' => 2, 'daily_rate' => 2000, 'total_price' => 4000, 'status' => 'confirmed']);
    $b2 = Booking::create(['booking_code' => 'LNR-FILTER-2', 'user_id' => $user->id, 'vehicle_id' => $vehicle2->id, 'customer_name' => 'Customer Two', 'customer_email' => 'c2@example.com', 'customer_phone' => '222', 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+2 days')), 'total_days' => 2, 'daily_rate' => 2200, 'total_price' => 4400, 'status' => 'confirmed']);

    $response = $this->actingAs($user)->get(route('calendar.index', ['vehicle_id' => $vehicle1->id]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Calendar/Index')->has('bookings', 1)->where('bookings.0.customer_name', 'Customer One'));
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
    $responseDefault->assertInertia(fn ($page) => $page->component('Calendar/Index')->has('bookings', 1)->where('bookings.0.customer_name', 'Booking For A'));

    // Filtering explicitly for All Users sees both
    $responseAll = $this->actingAs($userA)->get(route('calendar.index', ['user_id' => 'all']));
    $responseAll->assertStatus(200);
    $responseAll->assertInertia(fn ($page) => $page->component('Calendar/Index')->has('bookings', 2));

    // Filtering explicitly for User B sees B's booking but not A's
    $responseUserB = $this->actingAs($userA)->get(route('calendar.index', ['user_id' => $userB->id]));
    $responseUserB->assertStatus(200);
    $responseUserB->assertInertia(fn ($page) => $page->component('Calendar/Index')->has('bookings', 1)->where('bookings.0.customer_name', 'Booking For B'));
});

test('can create vehicle data entry with photo upload', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('fortuner.jpg');

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
    expect((float) $booking->total_price)->toBe(1500.00); // 3 days * 500 destination rate

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
    expect((float) $booking->total_price)->toBe(1600.00); // 1 day * 1000 + 3 * 200
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
    $response->assertInertia(fn ($page) => $page->component('Dashboard')->has('myVehicles', 1)->where('myVehicles.0.name', 'Host Montero Sport'));
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

test('confirmed booking allows editing all details', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'Sedan', 'category' => 'sedan']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Original Sedan',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => 'SED 111',
        'daily_rate' => 2000,
        'status' => 'available',
    ]);
    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 1500,
    ]);

    $booking = Booking::create([
        'booking_code' => 'LNR-CONFIRMED-EDIT',
        'vehicle_id' => $vehicle->id,
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'destination' => 'Butuan City, Agusan del Norte',
        'destination_rate' => 1500,
        'customer_name' => 'Old Name',
        'customer_phone' => '09111111111',
        'pickup_location' => 'Old Location',
        'pickup_time' => '08:00',
        'return_time' => '17:00',
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'total_days' => 2,
        'daily_rate' => 2000,
        'total_price' => 3000,
        'status' => 'confirmed',
    ]);

    // View edit page
    $responseEdit = $this->actingAs($user)->get(route('bookings.edit', $booking->id));
    $responseEdit->assertStatus(200);
    $responseEdit->assertInertia(fn ($page) => $page->component('Bookings/Edit')->where('canEditAll', true));

    // Update all details
    $responseUpdate = $this->actingAs($user)->put(route('bookings.update', $booking->id), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Updated Customer Name',
        'customer_phone' => '09999999999',
        'pickup_location' => 'New Airport Location',
        'pickup_time' => '09:00',
        'return_time' => '18:00',
        'start_date' => '2026-12-05',
        'end_date' => '2026-12-07',
        'notes' => 'Updated notes',
        'status' => 'confirmed',
    ]);

    $responseUpdate->assertRedirect(route('bookings.show', $booking->id));

    $booking->refresh();
    expect($booking->customer_name)->toBe('Updated Customer Name');
    expect($booking->customer_phone)->toBe('09999999999');
    expect($booking->pickup_location)->toBe('New Airport Location');
    expect($booking->start_date->format('Y-m-d'))->toBe('2026-12-05');
});

test('completed or cancelled booking cannot be updated anymore', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'SUV', 'category' => 'suv']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Original SUV',
        'make' => 'Toyota',
        'model' => 'Fortuner',
        'year' => 2024,
        'license_plate' => 'SUV 222',
        'daily_rate' => 3500,
        'status' => 'available',
    ]);
    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Cabadbaran City',
        'destination_rate' => 2000,
    ]);

    $booking = Booking::create([
        'booking_code' => 'LNR-COMPLETED-LOCK',
        'vehicle_id' => $vehicle->id,
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'destination' => 'Cabadbaran City, Agusan del Norte',
        'destination_rate' => 2000,
        'customer_name' => 'Locked Customer Name',
        'customer_phone' => '09122222222',
        'pickup_location' => 'Locked Location',
        'pickup_time' => '08:00',
        'return_time' => '17:00',
        'start_date' => '2026-12-10',
        'end_date' => '2026-12-12',
        'total_days' => 3,
        'daily_rate' => 3500,
        'total_price' => 6000,
        'status' => 'completed',
    ]);

    // View edit page
    $responseEdit = $this->actingAs($user)->get(route('bookings.edit', $booking->id));
    $responseEdit->assertStatus(200);
    $responseEdit->assertInertia(fn ($page) => $page->component('Bookings/Edit')->where('canEditAll', false));

    // Attempt updating status
    $responseUpdate = $this->actingAs($user)->put(route('bookings.update', $booking->id), [
        'customer_name' => 'Should Not Change',
        'status' => 'cancelled',
        'notes' => 'Attempted cancellation',
    ]);

    $responseUpdate->assertRedirect(route('bookings.show', $booking->id));
    $responseUpdate->assertSessionHas('error');

    $booking->refresh();
    expect($booking->status)->toBe('completed');
    expect($booking->customer_name)->toBe('Locked Customer Name');
});

test('booking cannot be updated to pending status', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'Sedan', 'category' => 'sedan']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Sedan Vios',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => 'SED 999',
        'daily_rate' => 2000,
        'status' => 'available',
    ]);
    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 1500,
    ]);

    $booking = Booking::create([
        'booking_code' => 'LNR-NO-PENDING',
        'vehicle_id' => $vehicle->id,
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'destination' => 'Butuan City, Agusan del Norte',
        'destination_rate' => 1500,
        'customer_name' => 'Test User',
        'customer_phone' => '09111111111',
        'pickup_location' => 'Airport',
        'pickup_time' => '08:00',
        'return_time' => '17:00',
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'total_days' => 2,
        'daily_rate' => 2000,
        'total_price' => 3000,
        'status' => 'confirmed',
    ]);

    // Quick status update with pending should fail validation
    $responseQuick = $this->actingAs($user)->patch(route('bookings.update', $booking->id), [
        'status' => 'pending',
    ]);
    $responseQuick->assertSessionHasErrors('status');

    // Full form update with pending should also fail validation
    $responseFull = $this->actingAs($user)->put(route('bookings.update', $booking->id), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Test User',
        'customer_phone' => '09111111111',
        'pickup_location' => 'Airport',
        'pickup_time' => '08:00',
        'return_time' => '17:00',
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-02',
        'status' => 'pending',
    ]);
    $responseFull->assertSessionHasErrors('status');
});

test('can render edit vehicle page with all vehicle details', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'Sedan', 'category' => 'sedan']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Toyota Vios 1.3 XLE',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2023,
        'license_plate' => 'ABC 9999',
        'color' => 'Silver Metallic',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_rate' => 1800.00,
        'status' => 'available',
        'description' => 'Clean city car',
    ]);

    $response = $this->actingAs($user)->get(route('vehicles.edit', $vehicle->id));
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Vehicles/Edit')
        ->where('vehicle.name', 'Toyota Vios 1.3 XLE')
        ->where('vehicle.make', 'Toyota')
        ->where('vehicle.model', 'Vios')
        ->where('vehicle.license_plate', 'ABC 9999')
        ->where('vehicle.color', 'Silver Metallic')
        ->where('vehicle.transmission', 'Automatic')
        ->where('vehicle.fuel_type', 'Gasoline')
        ->where('vehicle.seats', 5)
        ->has('users')
        ->has('vehicleTypes')
    );
});

test('can update all details of a vehicle', function () {
    $user = User::factory()->create();
    $vehicleType = VehicleType::create(['name' => 'Sedan', 'category' => 'sedan']);
    $newVehicleType = VehicleType::create(['name' => 'SUV', 'category' => 'suv']);
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'vehicle_type_id' => $vehicleType->id,
        'name' => 'Toyota Vios 1.3 XLE',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2023,
        'license_plate' => 'ABC 9999',
        'color' => 'Silver Metallic',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_rate' => 1800.00,
        'status' => 'available',
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle->id), [
        'name' => 'Toyota Vios Upgraded',
        'make' => 'Toyota',
        'model' => 'Vios GR-S',
        'year' => 2024,
        'license_plate' => 'ABC 9999',
        'color' => 'Super Red',
        'transmission' => 'Manual',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_rate' => 2200.00,
        'status' => 'maintenance',
        'description' => 'Updated specs and features',
        'vehicle_type_id' => $newVehicleType->id,
    ]);

    $response->assertRedirect(route('vehicles.index'));

    $vehicle->refresh();
    expect($vehicle->name)->toBe('Toyota Vios Upgraded');
    expect($vehicle->model)->toBe('Vios GR-S');
    expect($vehicle->year)->toBe(2024);
    expect($vehicle->color)->toBe('Super Red');
    expect($vehicle->transmission)->toBe('Manual');
    expect($vehicle->daily_rate)->toBe('2200.00');
    expect($vehicle->status)->toBe('maintenance');
    expect($vehicle->description)->toBe('Updated specs and features');
    expect($vehicle->vehicle_type_id)->toBe($newVehicleType->id);
});

test('prevents registering vehicle with duplicate license plate', function () {
    $userA = User::factory()->create(['name' => 'User A']);
    $userB = User::factory()->create(['name' => 'User B']);

    Vehicle::create([
        'user_id' => $userA->id,
        'name' => 'User A Vehicle',
        'make' => 'Honda',
        'model' => 'City',
        'year' => 2023,
        'license_plate' => 'NBD 1234',
        'daily_rate' => 2000.00,
        'status' => 'available',
    ]);

    // User B tries to register same license plate (even lowercase with spaces)
    $response = $this->actingAs($userB)->post(route('vehicles.store'), [
        'name' => 'User B Vehicle',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => '  nbd 1234  ',
        'color' => 'White',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_rate' => 2100.00,
        'status' => 'available',
    ]);

    $response->assertSessionHasErrors('license_plate');
    expect(Vehicle::where('license_plate', 'NBD 1234')->count())->toBe(1);
});

test('prevents updating vehicle to another vehicle license plate', function () {
    $user = User::factory()->create();

    $vehicle1 = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Car 1',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2023,
        'license_plate' => 'CAR 1111',
        'daily_rate' => 1500.00,
        'status' => 'available',
    ]);

    $vehicle2 = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Car 2',
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2024,
        'license_plate' => 'CAR 2222',
        'daily_rate' => 2500.00,
        'status' => 'available',
    ]);

    // Try updating Car 2 to have Car 1's license plate
    $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle2->id), [
        'name' => 'Car 2 Updated',
        'make' => 'Honda',
        'model' => 'Civic',
        'year' => 2024,
        'license_plate' => 'car 1111',
        'transmission' => 'Automatic',
        'fuel_type' => 'Gasoline',
        'seats' => 5,
        'daily_rate' => 2500.00,
        'status' => 'available',
    ]);

    $response->assertSessionHasErrors('license_plate');
});

test('prevents updating booking to dates/times that conflict with another booking for the same vehicle', function () {
    $user = User::factory()->create();

    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Fortuner Test',
        'make' => 'Toyota',
        'model' => 'Fortuner',
        'year' => 2024,
        'license_plate' => 'FORT 9999',
        'daily_rate' => 3000.00,
        'status' => 'available',
    ]);

    $destination = Destination::create([
        'region' => 'Region X',
        'province' => 'Misamis Oriental',
        'city' => 'Cagayan de Oro',
        'destination_rate' => 1500.00,
    ]);

    // Existing confirmed booking: Nov 10 09:00 to Nov 12 17:00
    Booking::create([
        'booking_code' => 'LNR-EXIST-111',
        'vehicle_id' => $vehicle->id,
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'destination' => 'Cagayan de Oro, Misamis Oriental',
        'destination_rate' => 1500.00,
        'customer_name' => 'Existing Customer',
        'customer_phone' => '09123456789',
        'pickup_location' => 'Airport',
        'pickup_time' => '09:00',
        'return_time' => '17:00',
        'start_date' => '2026-11-10',
        'end_date' => '2026-11-12',
        'total_days' => 3,
        'daily_rate' => 3000.00,
        'total_price' => 10500.00,
        'status' => 'confirmed',
    ]);

    // Booking to edit
    $bookingToEdit = Booking::create([
        'booking_code' => 'LNR-CONF-222',
        'vehicle_id' => $vehicle->id,
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'destination' => 'Cagayan de Oro, Misamis Oriental',
        'destination_rate' => 1500.00,
        'customer_name' => 'Edit Customer',
        'customer_phone' => '09987654321',
        'pickup_location' => 'Downtown',
        'pickup_time' => '08:00',
        'return_time' => '18:00',
        'start_date' => '2026-11-20',
        'end_date' => '2026-11-22',
        'total_days' => 3,
        'daily_rate' => 3000.00,
        'total_price' => 10500.00,
        'status' => 'confirmed',
    ]);

    // Attempt to update bookingToEdit to overlap with existing booking: Nov 11 to Nov 13
    $response = $this->actingAs($user)->put(route('bookings.update', $bookingToEdit->id), [
        'vehicle_id' => $vehicle->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Edit Customer',
        'customer_phone' => '09987654321',
        'pickup_location' => 'Downtown',
        'pickup_time' => '10:00',
        'return_time' => '12:00',
        'start_date' => '2026-11-11',
        'end_date' => '2026-11-13',
        'status' => 'confirmed',
    ]);

    $response->assertSessionHasErrors('start_date');
    $errors = session('errors')->get('start_date');
    expect($errors[0])->toContain('Schedule Conflict: Vehicle');
});

test('vehicle is marked as rented today when it has a confirmed booking matching today', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Active Rental Car',
        'make' => 'Toyota',
        'model' => 'Fortuner',
        'year' => 2024,
        'license_plate' => 'RNT 1111',
        'daily_rate' => 3500.00,
        'status' => 'available',
    ]);

    $destination = Destination::create([
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Butuan City',
        'destination_rate' => 500.00,
    ]);

    // Create confirmed booking for today
    Booking::create([
        'booking_code' => 'LNR-TODAY-01',
        'vehicle_id' => $vehicle->id,
        'user_id' => $user->id,
        'destination_id' => $destination->id,
        'customer_name' => 'Current Customer',
        'customer_phone' => '09123456789',
        'pickup_location' => 'Airport',
        'pickup_time' => '08:00',
        'return_time' => '18:00',
        'start_date' => date('Y-m-d'),
        'end_date' => date('Y-m-d', strtotime('+1 day')),
        'total_days' => 2,
        'daily_rate' => 3500.00,
        'total_price' => 7500.00,
        'status' => 'confirmed',
    ]);

    expect($vehicle->is_rented_today)->toBeTrue();

    // Check All Listings page reflects rented status
    $response = $this->actingAs($user)->get(route('vehicles.all'));
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Vehicles/AllListing')
        ->where('vehicles.0.is_rented_today', true)
    );
});

test('cannot create booking for a vehicle with status other than available', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Broken Vehicle',
        'make' => 'Honda',
        'model' => 'City',
        'year' => 2023,
        'license_plate' => 'MAINT 999',
        'daily_rate' => 2000.00,
        'status' => 'maintenance',
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
        'customer_phone' => '09123456789',
        'pickup_location' => 'Garage',
        'pickup_time' => '09:00',
        'return_time' => '09:00',
        'start_date' => date('Y-m-d', strtotime('+5 days')),
        'end_date' => date('Y-m-d', strtotime('+6 days')),
    ]);

    $response->assertSessionHasErrors('vehicle_id');
});

test('cannot create booking for a past start date', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Past Booking Vehicle',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => 'PAST 123',
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
        'customer_name' => 'Past Customer',
        'customer_phone' => '09123456789',
        'pickup_location' => 'Airport',
        'pickup_time' => '09:00',
        'return_time' => '09:00',
        'start_date' => date('Y-m-d', strtotime('-2 days')),
        'end_date' => date('Y-m-d', strtotime('-1 day')),
    ]);

    $response->assertSessionHasErrors('start_date');
});

test('bookings.create passes selectedVehicleId matching vehicle_id query param', function () {
    $user = User::factory()->create();
    $vehicle1 = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'First Vehicle',
        'make' => 'Toyota',
        'model' => 'Vios',
        'year' => 2024,
        'license_plate' => 'ABC 111',
        'daily_rate' => 2000.00,
        'status' => 'available',
    ]);

    $vehicle2 = Vehicle::create([
        'user_id' => $user->id,
        'name' => 'Second Vehicle',
        'make' => 'Honda',
        'model' => 'City',
        'year' => 2024,
        'license_plate' => 'XYZ 222',
        'daily_rate' => 2500.00,
        'status' => 'available',
    ]);

    $response = $this->actingAs($user)->get(route('bookings.create', ['vehicle_id' => $vehicle2->id]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('Bookings/Create')
        ->where('selectedVehicleId', (string) $vehicle2->id)
        ->where('preselectedVehicleId', (string) $vehicle2->id)
    );
});
