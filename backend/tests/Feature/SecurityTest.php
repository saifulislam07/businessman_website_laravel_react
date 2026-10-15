<?php

it('redirects the bare API domain to the public site', function () {
    config(['portfolio.frontend_url' => 'https://example.com']);

    $this->get('/')->assertRedirect('https://example.com');
});

it('asks search engines not to index the API host', function () {
    expect(file_get_contents(public_path('robots.txt')))->toContain('Disallow: /');
});

it('does not leak internals in error responses when debug is off', function () {
    config(['app.debug' => false]);

    $json = $this->getJson('/api/v1/does-not-exist')->assertNotFound()->json();

    expect($json)->toHaveKey('message')->not->toHaveKeys(['trace', 'file', 'line', 'exception']);
});

it('only allows the configured frontend origin for CORS', function () {
    expect(config('cors.allowed_origins'))->toBe([env('FRONTEND_URL', 'http://localhost:5173')]);
});

it('keeps the admin panel behind authentication', function () {
    foreach (['/admin', '/admin/stats', '/admin/ventures', '/admin/contact-messages', '/admin/site-settings'] as $path) {
        $this->get($path)->assertRedirect('/admin/login');
    }
});
