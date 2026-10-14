<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen on desktop', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can authenticate using the login screen on mobile', function () {
    $user = User::factory()->create();

    $response = $this->withHeader('User-Agent', 'iPhone')->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'is_mobile' => '1',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('menu', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('login automatically issues remember me persistent cookie by default', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);

    $recallerName = Auth::guard('web')->getRecallerName();
    $recallerCookie = $response->getCookie($recallerName);

    expect($recallerCookie)->not->toBeNull();
    expect($user->fresh()->remember_token)->not->toBeNull();
});

test('login enforces persistent remember me even without remember checkbox checked in request', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'remember' => false,
    ]);

    $this->assertAuthenticatedAs($user);

    $recallerName = Auth::guard('web')->getRecallerName();
    $recallerCookie = $response->getCookie($recallerName);

    expect($recallerCookie)->not->toBeNull();
});

test('authenticated user session persists across subsequent requests', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    // Subsequent page refresh/visit retains authentication
    $response = $this->get('/dashboard');
    $response->assertStatus(200);
    $this->assertAuthenticatedAs($user);
});

test('user stays authenticated via remember cookie even when session is purged', function () {
    $user = User::factory()->create();

    $loginResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $recallerName = Auth::guard('web')->getRecallerName();
    $recallerCookie = $loginResponse->getCookie($recallerName);
    expect($recallerCookie)->not->toBeNull();

    // Flush active in-memory session (simulating browser restart where only cookies remain, or session purge)
    session()->flush();
    Auth::guard('web')->forgetUser();

    // Next request carries the persistent recaller cookie
    $response = $this->withCookie($recallerName, $recallerCookie->getValue())
        ->get('/dashboard');

    $response->assertStatus(200);
    $this->assertAuthenticatedAs($user);
});

test('logout clears user session, removes recaller cookie, and cycles remember token', function () {
    $user = User::factory()->create();

    $loginResponse = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $originalToken = $user->fresh()->remember_token;

    $logoutResponse = $this->post('/logout');

    $this->assertGuest();
    $logoutResponse->assertRedirect('/');

    // Token has been cycled
    expect($user->fresh()->remember_token)->not->toEqual($originalToken);

    // Recaller cookie is queued to be forgotten / deleted
    $recallerName = Auth::guard('web')->getRecallerName();
    $deletedCookie = $logoutResponse->getCookie($recallerName);
    expect($deletedCookie)->not->toBeNull();
    expect($deletedCookie->getExpiresTime())->toBeLessThan(time());
});

test('deactivated or unapproved user is automatically logged out on subsequent visit', function () {
    $user = User::factory()->create([
        'is_approved' => true,
    ]);

    $this->actingAs($user);

    // User is deactivated/unapproved by admin
    $user->update(['is_approved' => false]);

    $response = $this->get('/dashboard');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('session and remember configurations are set for indefinite persistent login', function () {
    expect((int) config('session.lifetime'))->toBeGreaterThanOrEqual(52560000);
    expect(config('session.expire_on_close'))->toBeFalse();
    expect((int) config('auth.guards.web.remember'))->toBeGreaterThanOrEqual(52560000);
    expect(config('session.http_only'))->toBeTrue();
    expect(config('session.same_site'))->toEqual('lax');
});

test('admin deleting a user invalidates user active database sessions', function () {
    $admin = User::factory()->create([
        'is_approved' => true,
        'is_admin' => true,
    ]);

    $targetUser = User::factory()->create([
        'is_approved' => true,
    ]);

    // Create a mock active session in database for the target user
    DB::table('sessions')->insert([
        'id' => 'session_test_'.Str::random(20),
        'user_id' => $targetUser->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'TestBrowser',
        'payload' => base64_encode(serialize(['_token' => 'mock_token'])),
        'last_activity' => time(),
    ]);

    expect(DB::table('sessions')->where('user_id', $targetUser->id)->count())->toBe(1);

    // Admin destroys the user
    $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $targetUser->id));

    $response->assertRedirect();
    expect(DB::table('sessions')->where('user_id', $targetUser->id)->count())->toBe(0);
});
