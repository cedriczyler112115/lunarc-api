<?php

it('redirects root to dashboard on desktop', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('dashboard'));
});

it('redirects root to menu on mobile', function () {
    $response = $this->withHeader('User-Agent', 'iPhone')->get('/');

    $response->assertRedirect(route('menu'));
});
