<?php

use App\Models\User;
use App\Models\Destination;

test('authenticated user can view destinations index list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('destinations.index'));

    $response->assertStatus(200);
});

test('can create a new destination with custom rental price', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('destinations.store'), [
        'region' => 'Region XIII (Caraga)',
        'province' => 'Agusan del Norte',
        'city' => 'Test Destination City',
        'destination_rate' => 1250.00,
        'description' => 'Test description route',
    ]);

    $response->assertRedirect(route('destinations.index'));

    $destination = Destination::where('city', 'Test Destination City')->first();
    expect($destination)->not->toBeNull();
    expect((float)$destination->destination_rate)->toBe(1250.00);
});

test('can update destination rental price', function () {
    $user = User::factory()->create();
    $destination = Destination::create([
        'region' => 'Region XI (Davao)',
        'province' => 'Davao del Sur',
        'city' => 'Update Test City',
        'destination_rate' => 2000.00,
    ]);

    $response = $this->actingAs($user)->put(route('destinations.update', $destination->id), [
        'region' => 'Region XI (Davao)',
        'province' => 'Davao del Sur',
        'city' => 'Update Test City',
        'destination_rate' => 3500.00,
        'description' => 'Updated price description',
    ]);

    $response->assertRedirect(route('destinations.index'));
    expect((float)$destination->fresh()->destination_rate)->toBe(3500.00);
});

test('can update destination rental price inline from table', function () {
    $user = User::factory()->create();
    $destination = Destination::create([
        'region' => 'Region XI (Davao)',
        'province' => 'Davao del Sur',
        'city' => 'Inline Edit Test City',
        'destination_rate' => 1500.00,
    ]);

    $response = $this->actingAs($user)->patch(route('destinations.update-price', $destination->id), [
        'destination_rate' => 2800.00,
    ]);

    $response->assertRedirect();
    expect((float)$destination->fresh()->destination_rate)->toBe(2800.00);
});

test('can delete a destination entry', function () {
    $user = User::factory()->create();
    $destination = Destination::create([
        'region' => 'Region X (Northern Mindanao)',
        'province' => 'Bukidnon',
        'city' => 'Delete Test City',
        'destination_rate' => 1500.00,
    ]);

    $response = $this->actingAs($user)->delete(route('destinations.destroy', $destination->id));

    $response->assertRedirect(route('destinations.index'));
    expect(Destination::find($destination->id))->toBeNull();
});
