<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new guest can register with basic info', function () {
    $response = $this->post('/register', [
        'role' => 'guest',
        'first_name' => 'Juana',
        'last_name' => 'Dela Cruz',
        'middle_name' => 'Santos',
        'extension_name' => 'Jr.',
        'birthday' => '1995-05-15',
        'address' => '123 Main St, Butuan City',
        'contact_number' => '09171112222',
        'email' => 'guestnew@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    $user = User::where('email', 'guestnew@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('guest');
    expect($user->first_name)->toBe('Juana');
    expect($user->last_name)->toBe('Dela Cruz');
});

test('new car owner requires owner description during registration', function () {
    // Without owner_description -> error
    $responseFailed = $this->post('/register', [
        'role' => 'car_owner',
        'first_name' => 'Pedro',
        'last_name' => 'Penduko',
        'birthday' => '1990-01-01',
        'address' => '456 Fleet Ave, Butuan City',
        'contact_number' => '09183334444',
        'email' => 'ownerfailed@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $responseFailed->assertSessionHasErrors('owner_description');

    // With owner_description -> success
    $responseSuccess = $this->post('/register', [
        'role' => 'car_owner',
        'first_name' => 'Pedro',
        'last_name' => 'Penduko',
        'birthday' => '1990-01-01',
        'address' => '456 Fleet Ave, Butuan City',
        'contact_number' => '09183334444',
        'owner_description' => 'Owner of 5 premium rental SUVs in Caraga Region.',
        'email' => 'ownersuccess@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $responseSuccess->assertRedirect(route('login'));

    $user = User::where('email', 'ownersuccess@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->role)->toBe('car_owner');
    expect($user->owner_description)->toBe('Owner of 5 premium rental SUVs in Caraga Region.');
});
