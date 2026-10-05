<?php

use App\Models\User;

test('registration view can be rendered from login button', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Auth/Register'));
});

test('new user registration is pending approval and cannot log in until approved', function () {
    $response = $this->post(route('register'), [
        'role' => 'guest',
        'first_name' => 'New',
        'last_name' => 'Pending User',
        'birthday' => '1995-01-01',
        'address' => '123 Test St',
        'contact_number' => '09170000000',
        'email' => 'pending.user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    $user = User::where('email', 'pending.user@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->is_approved)->toBeFalse();

    // Attempt login with pending user
    $loginResponse = $this->post(route('login'), [
        'email' => 'pending.user@example.com',
        'password' => 'password123',
    ]);

    $loginResponse->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('admin can view users list and approve pending user', function () {
    $admin = User::factory()->create([
        'is_approved' => true,
        'is_admin' => true,
    ]);

    $pendingUser = User::factory()->create([
        'is_approved' => false,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.users.index'));
    $response->assertStatus(200);
    $response->assertSee($pendingUser->email);

    // Admin approves user
    $approveResponse = $this->actingAs($admin)->post(route('admin.users.approve', $pendingUser->id));
    $approveResponse->assertRedirect();

    expect($pendingUser->fresh()->is_approved)->toBeTrue();

    // Logout admin session
    auth()->logout();

    // Now approved user can log in
    $loginResponse = $this->post(route('login'), [
        'email' => $pendingUser->email,
        'password' => 'password', // Default factory password
    ]);

    $this->assertAuthenticatedAs($pendingUser);
});
