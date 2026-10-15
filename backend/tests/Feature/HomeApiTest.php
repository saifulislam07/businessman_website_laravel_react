<?php

use App\Models\Setting;
use App\Models\Venture;
use Illuminate\Support\Facades\Cache;

it('returns every section with only active, ordered items', function () {
    Setting::create(['key' => 'name', 'value' => 'Fayer Ahammed']);
    Venture::create(['name' => 'Second', 'slug' => 'second', 'industry' => 'Tech', 'country' => 'MY', 'sort_order' => 2]);
    Venture::create(['name' => 'First', 'slug' => 'first', 'industry' => 'Trade', 'country' => 'BN', 'sort_order' => 1]);
    Venture::create(['name' => 'Hidden', 'slug' => 'hidden', 'industry' => 'Tech', 'country' => 'BN', 'is_active' => false]);

    $response = $this->getJson('/api/v1/home')->assertOk();

    $response->assertJsonStructure(['data' => [
        'settings', 'about', 'stats', 'ventures', 'expertise',
        'milestones', 'gallery', 'partners', 'testimonials',
    ]]);

    expect($response->json('data.settings.name'))->toBe('Fayer Ahammed')
        ->and(collect($response->json('data.ventures'))->pluck('name')->all())->toBe(['First', 'Second']);
});

it('caches the response and clears the cache when content changes', function () {
    $this->getJson('/api/v1/home')->assertOk();
    expect(Cache::has('home'))->toBeTrue();

    Venture::create(['name' => 'New', 'slug' => 'new', 'industry' => 'Tech', 'country' => 'BN']);
    expect(Cache::has('home'))->toBeFalse();

    $this->getJson('/api/v1/home')->assertJsonCount(1, 'data.ventures');
});

it('serves settings, ventures and filtered gallery', function () {
    Setting::create(['key' => 'tagline', 'value' => 'Hello']);

    $this->getJson('/api/v1/settings')->assertOk()->assertJsonPath('data.tagline', 'Hello');
    $this->getJson('/api/v1/ventures')->assertOk()->assertJsonStructure(['data']);
    $this->getJson('/api/v1/gallery?category=Events')->assertOk()->assertJsonStructure(['data']);
});
