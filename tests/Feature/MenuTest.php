<?php

use App\Models\User;

test('authenticated user can view menu page', function () {
    $user = User::factory()->create(['is_approved' => true]);

    $response = $this->actingAs($user)->get(route('menu'));

    $response->assertStatus(200);
    $response->assertSee('App Menu');
    $response->assertSee('Vehicle Entry');
    $response->assertSee('Destinations');
    $response->assertSee('Bookings');
    $response->assertSee('Schedule View');
});

test('root path redirects to menu page', function () {
    $user = User::factory()->create(['is_approved' => true]);

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('menu'));
});
