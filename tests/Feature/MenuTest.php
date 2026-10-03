<?php

use App\Models\User;

test('authenticated user on mobile can view menu page', function () {
    $user = User::factory()->create(['is_approved' => true, 'is_admin' => false]);

    $response = $this->actingAs($user)->withHeader('User-Agent', 'iPhone')->get(route('menu'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
    $response->assertSee('My Fleet');
    $response->assertSee('My Bookings');
    $response->assertSee('My Calendar');
    $response->assertDontSee('User Approvals');
});

test('authenticated admin on mobile can see Rates and Users in menu page', function () {
    $admin = User::factory()->create(['is_approved' => true, 'is_admin' => true]);

    $response = $this->actingAs($admin)->withHeader('User-Agent', 'iPhone')->get(route('menu'));

    $response->assertStatus(200);
    $response->assertSee('Rates');
    $response->assertSee('Users');
});

test('desktop user accessing menu page is redirected to dashboard', function () {
    $user = User::factory()->create(['is_approved' => true]);

    $response = $this->actingAs($user)->get(route('menu'));

    $response->assertRedirect(route('dashboard'));
});

test('root path redirects to dashboard on desktop', function () {
    $user = User::factory()->create(['is_approved' => true]);

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('dashboard'));
});

test('root path redirects to menu on mobile', function () {
    $user = User::factory()->create(['is_approved' => true]);

    $response = $this->actingAs($user)->withHeader('User-Agent', 'iPhone')->get('/');

    $response->assertRedirect(route('menu'));
});
